<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\LayananPublik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalChats   = Chat::count();
        $totalLayanan = LayananPublik::count();
        $totalValid   = LayananPublik::where('validation_status', 'valid')->count();
        $totalRevisi  = LayananPublik::where('validation_status', 'revisi')->count();
        $totalPending = LayananPublik::where('validation_status', 'pending')->count();
        $totalSalah   = LayananPublik::where('validation_status', 'salah_mapping')->count();

        // Chat hari ini vs kemarin
        $chatsToday     = Chat::whereDate('created_at', Carbon::today())->count();
        $chatsYesterday = Chat::whereDate('created_at', Carbon::yesterday())->count();

        // Chat per hari (7 hari terakhir)
        $chatPerHari = Chat::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // Isi tanggal yang kosong agar grafik lengkap
        $chartLabels = [];
        $chartData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date          = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::now()->subDays($i)->translatedFormat('D, d M');
            $chartData[]   = $chatPerHari[$date]->jumlah ?? 0;
        }

        // Statistik dinas terpopuler
        $dinasStats = LayananPublik::select('dinas', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->groupBy('dinas')
            ->orderByDesc('jumlah')
            ->limit(8)
            ->get();

        // Chat terbaru
        $recentChats = Chat::latest()->limit(5)->get();

        // Layanan pending terbaru
        $pendingLayanan = LayananPublik::where('validation_status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalChats',
            'totalLayanan',
            'totalValid',
            'totalRevisi',
            'totalPending',
            'totalSalah',
            'chatsToday',
            'chatsYesterday',
            'chartLabels',
            'chartData',
            'dinasStats',
            'recentChats',
            'pendingLayanan'
        ));
    }

    public function chartData()
    {
        $chatPerHari = Chat::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $dinasStats = LayananPublik::select('dinas', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->groupBy('dinas')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        return response()->json([
            'chatPerHari' => $chatPerHari,
            'dinasStats'  => $dinasStats,
        ]);
    }
}