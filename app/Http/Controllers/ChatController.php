<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\LayananPublik;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // Property untuk menyimpan hasil mapping Instagram sementara
    private $foundInstagram = null;

    /**
     * Menampilkan halaman chat utama tanpa riwayat sebelumnya.
     */
    public function index() {
        // Baris di bawah diubah menjadi koleksi kosong agar tampilan bersih
        $history = collect(); 
        return view('chat', compact('history'));
    }

    /**
     * Deteksi apakah pertanyaan tentang wilayah LAIN (bukan PPU)
     */
    private function isAskingAboutOtherRegion($message) {
        $message = strtolower($message);
        
        // Daftar wilayah/kota LAIN yang harus ditolak
        $otherRegions = [
            'jakarta', 'surabaya', 'bandung', 'medan', 'makassar', 'semarang',
            'palembang', 'tangerang', 'depok', 'bekasi', 'solo', 'malang',
            'yogyakarta', 'jogja', 'bali', 'denpasar', 'lombok', 'aceh',
            'padang', 'pekanbaru', 'jambi', 'bengkulu', 'lampung', 'bangka',
            'pontianak', 'samarinda', 'balikpapan', 'banjarmasin', 'palangkaraya',
            'manado', 'palu', 'kendari', 'gorontalo', 'ambon', 'jayapura',
            'sorong', 'kupang', 'mataram', 'banten', 'bogor', 'sukabumi',
            'cirebon', 'purwokerto', 'magelang', 'kediri', 'blitar', 'probolinggo'
        ];
        
        foreach ($otherRegions as $region) {
            if (str_contains($message, $region)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Filter Kata Kunci Wilayah (Strict)
     */
    private function isPpuRelated($message) {
        $message = strtolower($message);
        $mandatoryKeywords = [
            'ppu', 'penajam', 'paser', 'utara', 'sepaku', 'ikn', 'nusantara', 
            'kaltim', 'kalimantan timur', 'babulu', 'waru', 'titik nol', 
            'bupati', 'diskominfo', 'dinas', 'desa', 'kecamatan', 'maridan', 'mentawir'
        ]; 
        $tambahanDinas = ['dishub', 'dinkes', 'kominfo', 'pendidikan', 'pekerjaan umum']; 
        
        foreach ($mandatoryKeywords as $keyword) {
            if (str_contains($message, $keyword)) return true;
        }
        return false;
    }

    /**
     * Mengambil data real-time dari Serper API
     */
    private function fetchLiveContext($query) {
        $serperKey = env('SERPER_API_KEY');
        if (!$serperKey) return "";

        try {
            $response = Http::withHeaders(['X-API-KEY' => $serperKey])
                ->post("https://google.serper.dev/search", [
                    'q' => $query . " Penajam Paser Utara IKN",
                    'gl' => 'id',
                    'hl' => 'id'
                ]);

            if ($response->successful()) {
                $results = $response->json()['organic'] ?? [];
                $context = "DATA TERKINI DARI INTERNET:\n";
                foreach (array_slice($results, 0, 4) as $res) {
                    $context .= "- " . $res['title'] . ": " . $res['snippet'] . "\n";
                }
                return $context;
            }
        } catch (\Exception $e) { return ""; }
        return "";
    }

    public function sendMessage(Request $request)
    {
        $apiKey = env('GROQ_API_KEY');
        $userMessage = trim($request->message);
        $layananData = LayananPublik::all();

        if (!$apiKey) {
            Log::error('GROQ_API_KEY not set in .env');
            return response()->json(['reply' => 'API Key tidak dikonfigurasi.'], 500);
        }

        $isShortGreeting = preg_match('/^(halo|hai|pagi|siang|sore|malam|p|permisi|asalamualaikum)$/i', $userMessage);
        $isAskingOtherRegion = $this->isAskingAboutOtherRegion($userMessage);
        $relatedToPpu = $this->isPpuRelated($userMessage);

        // TOLAK HANYA jika bertanya tentang wilayah LAIN
        if ($isAskingOtherRegion) {
            return response()->json([
                'reply' => "❌ **Akses Terbatas.**\n\nMaaf, saya adalah asisten digital khusus **Penajam Paser Utara (PPU)** & **IKN Nusantara**. Saya tidak dapat memberikan informasi tentang wilayah lain."
            ]);
        }

        try {
            $liveData = $relatedToPpu ? $this->fetchLiveContext($userMessage) : "";
            $layananContext = "DATA LAYANAN PUBLIK PPU:\n";
           
         
         if ($layananData->isNotEmpty()) {
            foreach ($layananData as $layanan) {
                $layananContext .= sprintf(
                    "ID %d:\n- Keluhan: %s\n- Solusi: %s\n- Dinas: %s\n- Link: %s\n- Instagram: %s\n\n",
                    $layanan->nomor ?? $layanan->id,
                    $layanan->keluhan,
                    $layanan->solusi,
                    $layanan->dinas ?? 'Tidak disebutkan',
                    $layanan->link ?? 'Tidak ada',
                    $layanan->instagram ?? 'Tidak ada'
                );
            }
        } else {
            $layananContext .= "Belum ada data layanan yang tersimpan.\n";
        }
           


            $systemPrompt = "Anda adalah PPU AI, asisten yang hanya memberikan data BENAR dan AKURAT tentang Penajam Paser Utara dan IKN.
            TUGAS ANDA:
        1. Analisis pesan user: \"" . $userMessage . "\".
        2. Cari kecocokan masalah tersebut dengan data referensi di bawah ini:
        
        DATA REFERENSI:
        " . $layananContext . "
        " . $liveData . "
        
        ATURAN JAWABAN:
        - Jika ditemukan kecocokan (misal: user tanya soal jalan, air, atau sampah), berikan jawaban yang menyebutkan:
            a. Dinas yang bertanggung jawab.
            b. Solusi teknis yang akan/sedang dilakukan.
            c. Link website dinas terkait.
        - Gunakan nada bicara yang sopan, profesional, dan solutif.
        - Jika masalah user TIDAK ADA dalam referensi, arahkan mereka untuk menghubungi portal umum Pemkab PPU namun tetap berikan saran yang masuk akal.
        - Jawab langsung ke inti masalah dengan format yang elegan.
### ATURAN UTAMA:
1. GUNAKAN DATA BERIKUT SEBAGAI REFERENSI UTAMA: \n$liveData
2. JAWABLAH HANYA berdasarkan fakta tentang wilayah PPU.
3. JANGAN PERNAH berhalusinasi.
4. JIKA USER bertanya di luar PPU/IKN, TOLAK DENGAN TEGAS.
5. Jika merujuk ke layanan publik, sebutkan nama Dinas terkait dan akun Instagramnya jika ada.
6. Gunakan bahasa Indonesia yang sopan.
7. Untuk pertanyaan umum tanpa kata kunci PPU, tetap jawab dengan ramah dan informatif.";

            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                "model" => "llama-3.3-70b-versatile",
                "messages" => [
                    ["role" => "system", "content" => $systemPrompt],
                    ["role" => "user", "content" => $userMessage]
                ],
                "temperature" => 0.0,
            ]);

            if ($response->successful()) {
                $aiReply = $response->json()['choices'][0]['message']['content'];
                
                // Simpan ke riwayat chat utama (Data tetap tersimpan di DB, tapi tidak ditampilkan di awal)
                Chat::create(['user_message' => $userMessage, 'ai_response' => $aiReply]);
                
                // Simpan ke tabel layanan_publik (Hanya jika terkait PPU & bukan greeting)
                // if ($relatedToPpu && !$isShortGreeting) {
                //     $this->saveToLayananPublik($userMessage, $aiReply, $liveData);
                // }
                
                return response()->json(['reply' => $aiReply]);
            }

            return response()->json(['reply' => 'Maaf, server sedang sibuk.'], 500);

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json(['reply' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    private function saveToLayananPublik($userMessage, $aiReply, $liveData) {
        try {
            $fullContent = $liveData . " " . $aiReply;
            
            // Reset mapping Instagram sebelum ekstraksi baru
            $this->foundInstagram = null;
            
            $dinas = $this->extractDinas($userMessage . " " . $aiReply);
            $instagram = $this->extractInstagram($aiReply);
            $links = $this->extractLinks($fullContent);

            LayananPublik::create([
                'keluhan' => $userMessage,
                'solusi' => $aiReply,
                'dinas' => $dinas ?? 'Pemerintah Kabupaten PPU',
                'link' => $links,
                'instagram' => $instagram ?? '@pemkab_ppu'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving to layanan_publik: ' . $e->getMessage());
        }
    }

    private function extractLinks($text) {
        preg_match_all('/(https?:\/\/[^\s\'"<>]+)/i', $text, $matches);
        $links = array_unique($matches[1] ?? []);
        return !empty($links) ? implode(' | ', array_slice($links, 0, 3)) : 'Tidak ada link referensi';
    }

    private function extractDinas($text) {
        $text = strtolower($text);
        $dinas_mapping = [
            'diskominfo' => ['name' => 'Dinas Komunikasi dan Informatika', 'ig' => '@diskominfoppu'],
            'kesehatan' => ['name' => 'Dinas Kesehatan', 'ig' => '@dinaskesppu'],
            'pendidikan' => ['name' => 'Dinas Pendidikan', 'ig' => '@dinaspendppu'],
            'pekerjaan umum' => ['name' => 'Dinas Pekerjaan Umum', 'ig' => '@dinaspuppu'],
            'pupr' => ['name' => 'Dinas PUPR', 'ig' => '@pupr_ppu'],
            'perhubungan' => ['name' => 'Dinas Perhubungan', 'ig' => '@dishub_ppu'],
        ];

        foreach ($dinas_mapping as $keyword => $data) {
            if (str_contains($text, $keyword)) {
                $this->foundInstagram = $data['ig'];
                return $data['name'];
            }
        }
        return null;
    }

    private function extractInstagram($text) {
        if ($this->foundInstagram) return $this->foundInstagram;
        
        preg_match('/(@[\w\.]+)/', $text, $matches);
        return $matches[1] ?? null;
    }
}