<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananPublik;
use Illuminate\Http\Request;

class ValidasiController extends Controller
{
    public function index(Request $request)
    {
        $query = LayananPublik::query();

        // Filter status
        if ($request->filled('status')) {
            $query->where('validation_status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $layanan = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => LayananPublik::count(),
            'pending' => LayananPublik::where('validation_status', 'pending')->count(),
            'valid'   => LayananPublik::where('validation_status', 'valid')->count(),
            'revisi'  => LayananPublik::where('validation_status', 'revisi')->count(),
            'salah'   => LayananPublik::where('validation_status', 'salah_mapping')->count(),
        ];

        return view('admin.validasi.index', compact('layanan', 'stats'));
    }

    public function updateStatus(Request $request, LayananPublik $layanan)
    {
        $validated = $request->validate([
            'validation_status' => ['required', 'in:pending,valid,revisi,salah_mapping'],
            'validation_note'   => ['nullable', 'string', 'max:1000'],
        ]);

        $layanan->update($validated);

        return redirect()->route('admin.validasi.index')
            ->with('success', "Status layanan #{$layanan->nomor} berhasil diperbarui.");
    }
}
