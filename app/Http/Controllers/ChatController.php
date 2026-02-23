<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\LayananPublik;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    private $foundInstagram = null;

    private $groqModels = [
        'llama-3.3-70b-versatile',
        'llama3-70b-8192',
        'mixtral-8x7b-32768',
    ];

    public function index()
    {
        $history = collect();
        return view('chat', compact('history'));
    }

    private function isAskingAboutOtherRegion($message)
    {
        $message = strtolower($message);
        $otherRegions = [
            'jakarta', 'surabaya', 'bandung', 'medan', 'makassar', 'semarang',
            'palembang', 'tangerang', 'depok', 'bekasi', 'solo', 'malang',
            'yogyakarta', 'jogja', 'bali', 'denpasar', 'lombok', 'aceh',
            'padang', 'pekanbaru', 'jambi', 'bengkulu', 'lampung', 'bangka',
            'pontianak', 'samarinda', 'balikpapan', 'banjarmasin', 'palangkaraya',
            'manado', 'palu', 'kendari', 'gorontalo', 'ambon', 'jayapura',
            'sorong', 'kupang', 'mataram', 'banten', 'bogor', 'sukabumi',
            'cirebon', 'purwokerto', 'magelang', 'kediri', 'blitar', 'probolinggo',
        ];
        foreach ($otherRegions as $region) {
            if (str_contains($message, $region)) return true;
        }
        return false;
    }

    private function isPpuRelated($message)
    {
        $message = strtolower($message);
        $keywords = [
            'ppu', 'penajam', 'paser', 'utara', 'sepaku', 'ikn', 'nusantara',
            'kaltim', 'kalimantan timur', 'babulu', 'waru', 'titik nol',
            'bupati', 'diskominfo', 'dinas', 'desa', 'kecamatan', 'maridan',
            'mentawir', 'api api', 'petung', 'penajam paser utara',
        ];
        foreach ($keywords as $kw) {
            if (str_contains($message, $kw)) return true;
        }
        return false;
    }

    /**
     * Ambil hanya layanan yang RELEVAN dengan pesan user.
     * KUNCI UTAMA agar token tidak meledak — tidak kirim semua data ke API.
     */
    private function getRelevantLayanan(string $userMessage, int $limit = 5): string
    {
        $message = strtolower($userMessage);
        $all     = LayananPublik::all();

        if ($all->isEmpty()) return "Belum ada data layanan.\n";

        $scored = $all->map(function ($item) use ($message) {
            $haystack = strtolower(
                ($item->keluhan ?? '') . ' ' .
                ($item->dinas   ?? '') . ' ' .
                ($item->solusi  ?? '')
            );
            $words = array_filter(explode(' ', $message), fn($w) => strlen($w) > 3);
            $score = 0;
            foreach ($words as $word) {
                if (str_contains($haystack, $word)) $score++;
            }
            return ['item' => $item, 'score' => $score];
        });

        $relevant = $scored
            ->filter(fn($x) => $x['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('item');

        // Fallback: ambil 3 data pertama jika tidak ada yang relevan
        if ($relevant->isEmpty()) {
            $relevant = $all->take(3);
        }

        $context = "";
        foreach ($relevant as $layanan) {
            $context .= sprintf(
                "- Keluhan: %s | Dinas: %s | Solusi: %s | Link: %s | IG: %s\n",
                $layanan->keluhan   ?? '-',
                $layanan->dinas     ?? '-',
                mb_substr($layanan->solusi ?? '-', 0, 120), // potong agar hemat token
                $layanan->link      ?? '-',
                $layanan->instagram ?? '-'
            );
        }

        return $context;
    }

    private function fetchLiveContext(string $query): string
    {
        $serperKey = env('SERPER_API_KEY');
        if (!$serperKey) return "";

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-KEY' => $serperKey])
                ->post("https://google.serper.dev/search", [
                    'q'  => $query . " Penajam Paser Utara IKN",
                    'gl' => 'id',
                    'hl' => 'id',
                ]);

            if ($response->successful()) {
                $results = $response->json()['organic'] ?? [];
                $context = "INFO TERKINI:\n";
                // Batasi 3 hasil, snippet max 100 karakter
                foreach (array_slice($results, 0, 3) as $res) {
                    $context .= "- " . mb_substr($res['title'] ?? '', 0, 80)
                              . ": " . mb_substr($res['snippet'] ?? '', 0, 100) . "\n";
                }
                return $context;
            }
        } catch (\Exception $e) {
            Log::warning('Serper error: ' . $e->getMessage());
        }

        return "";
    }

    private function callGroqApi(string $systemPrompt, string $userMessage): ?string
    {
        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            Log::error('GROQ_API_KEY tidak ditemukan di .env');
            return null;
        }

        foreach ($this->groqModels as $model) {
            try {
                Log::info("Mencoba model: {$model}");

                $response = Http::timeout(45)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])->post("https://api.groq.com/openai/v1/chat/completions", [
                    'model'       => $model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userMessage],
                    ],
                    'temperature' => 0.0,
                    'max_tokens'  => 768,
                ]);

                if ($response->successful()) {
                    $content = $response->json()['choices'][0]['message']['content'] ?? null;
                    if ($content) {
                        Log::info("Berhasil dengan model: {$model}");
                        return $content;
                    }
                }

                $status = $response->status();
                $body   = $response->body();
                Log::warning("Model {$model} gagal [{$status}]: {$body}");

                // API key salah — hentikan semua percobaan
                if ($status === 401) {
                    Log::error('API Key tidak valid (401).');
                    return null;
                }

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning("Koneksi gagal [{$model}]: " . $e->getMessage());
            } catch (\Exception $e) {
                Log::error("Exception [{$model}]: " . $e->getMessage());
            }
        }

        Log::error('Semua model Groq gagal.');
        return null;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage         = trim($request->message);
        $isAskingOtherRegion = $this->isAskingAboutOtherRegion($userMessage);
        $relatedToPpu        = $this->isPpuRelated($userMessage);
        $isShortGreeting     = (bool) preg_match(
            '/^(halo|hai|pagi|siang|sore|malam|p|permisi|assalamualaikum|asalamualaikum|hi|hello)$/i',
            $userMessage
        );

        if ($isAskingOtherRegion) {
            return response()->json([
                'reply' => "❌ **Akses Terbatas.**\n\nMaaf, saya hanya melayani informasi seputar **Penajam Paser Utara (PPU)** & **IKN Nusantara**.",
            ]);
        }

        // Hanya ambil layanan yang relevan (bukan semua data)
        $layananContext = $this->getRelevantLayanan($userMessage);

        // Live data hanya jika terkait PPU dan bukan greeting
        $liveData = ($relatedToPpu && !$isShortGreeting)
            ? $this->fetchLiveContext($userMessage)
            : "";

        // System prompt ringkas — tidak lebih dari ~500 token
        $systemPrompt = <<<PROMPT
Anda adalah PPU AI, asisten pelayanan publik Penajam Paser Utara (PPU) & IKN Nusantara.

ATURAN:
- Jawab hanya tentang PPU/IKN. Tolak pertanyaan wilayah lain.
- Gunakan data referensi di bawah. Jangan mengarang fakta.
- Sebutkan Dinas, solusi, dan link/Instagram jika tersedia.
- Jika data tidak ada, arahkan ke https://penajamkab.go.id
- Bahasa Indonesia, sopan, padat, solutif. Maksimal 3 paragraf.

REFERENSI LAYANAN:
{$layananContext}
{$liveData}
PROMPT;

        $aiReply = $this->callGroqApi($systemPrompt, $userMessage);

        if (!$aiReply) {
            return response()->json([
                'reply' => '⚠️ Layanan AI sedang tidak tersedia. Silakan coba lagi atau hubungi Pemkab PPU di https://penajamkab.go.id',
            ], 503);
        }

        try {
            Chat::create([
                'user_message' => $userMessage,
                'ai_response'  => $aiReply,
            ]);
        } catch (\Exception $e) {
            Log::warning('Gagal simpan chat: ' . $e->getMessage());
        }

        return response()->json(['reply' => $aiReply]);
    }

    // =========================================================
    //  HELPER — digunakan jika saveToLayananPublik diaktifkan
    // =========================================================

    private function saveToLayananPublik($userMessage, $aiReply, $liveData)
    {
        try {
            $this->foundInstagram = null;
            $dinas     = $this->extractDinas($userMessage . " " . $aiReply);
            $instagram = $this->extractInstagram($aiReply);
            $links     = $this->extractLinks($liveData . " " . $aiReply);

            LayananPublik::create([
                'keluhan'   => $userMessage,
                'solusi'    => $aiReply,
                'dinas'     => $dinas     ?? 'Pemerintah Kabupaten PPU',
                'link'      => $links,
                'instagram' => $instagram ?? '@pemkab_ppu',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving layanan_publik: ' . $e->getMessage());
        }
    }

    private function extractLinks($text): string
    {
        preg_match_all('/(https?:\/\/[^\s\'"<>]+)/i', $text, $matches);
        $links = array_unique($matches[1] ?? []);
        return !empty($links)
            ? implode(' | ', array_slice($links, 0, 3))
            : 'Tidak ada link referensi';
    }

    private function extractDinas($text): ?string
    {
        $text = strtolower($text);
        $map  = [
            'diskominfo'     => ['name' => 'Dinas Komunikasi dan Informatika', 'ig' => '@diskominfoppu'],
            'kesehatan'      => ['name' => 'Dinas Kesehatan',                  'ig' => '@dinaskesppu'],
            'pendidikan'     => ['name' => 'Dinas Pendidikan',                 'ig' => '@dinaspendppu'],
            'pekerjaan umum' => ['name' => 'Dinas Pekerjaan Umum',             'ig' => '@dinaspuppu'],
            'pupr'           => ['name' => 'Dinas PUPR',                       'ig' => '@pupr_ppu'],
            'perhubungan'    => ['name' => 'Dinas Perhubungan',                'ig' => '@dishub_ppu'],
            'lingkungan'     => ['name' => 'Dinas Lingkungan Hidup',           'ig' => '@dlhppu'],
            'sosial'         => ['name' => 'Dinas Sosial',                     'ig' => '@dinsos_ppu'],
        ];
        foreach ($map as $keyword => $data) {
            if (str_contains($text, $keyword)) {
                $this->foundInstagram = $data['ig'];
                return $data['name'];
            }
        }
        return null;
    }

    private function extractInstagram($text): ?string
    {
        if ($this->foundInstagram) return $this->foundInstagram;
        preg_match('/(@[\w\.]+)/', $text, $matches);
        return $matches[1] ?? null;
    }
}