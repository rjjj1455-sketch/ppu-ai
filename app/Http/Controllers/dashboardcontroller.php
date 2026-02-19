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
        $totalChats = Chat::count();
        $totalLayanan = LayananPublik::count();
        $totalValid = LayananPublik::where('validation_status', 'valid')->count();
        $totalRevisi = LayananPublik::where('validation_status', 'revisi')->count();

        // Chat per hari (7 hari terakhir)
        $chatPerHari = Chat::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Statistik dinas terpopuler
        $dinasStats = LayananPublik::select('dinas', DB::raw('COUNT(*) as jumlah'))
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->groupBy('dinas')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        // Chat terbaru
        $recentChats = Chat::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalChats',
            'totalLayanan',
            'totalValid',
            'totalRevisi',
            'chatPerHari',
            'dinasStats',
            'recentChats'
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
