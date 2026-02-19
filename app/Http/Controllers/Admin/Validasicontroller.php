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

        if ($request->filled('status')) {
            $query->where('validation_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('dinas')) {
            $query->filterDinas($request->dinas);
        }

        $layanan = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => LayananPublik::count(),
            'pending' => LayananPublik::where('validation_status', 'pending')->count(),
            'valid'   => LayananPublik::where('validation_status', 'valid')->count(),
            'revisi'  => LayananPublik::where('validation_status', 'revisi')->count(),
            'salah'   => LayananPublik::where('validation_status', 'salah_mapping')->count(),
        ];

        $dinasOptions = LayananPublik::select('dinas')
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->distinct()
            ->orderBy('dinas')
            ->pluck('dinas');

        return view('admin.validasi.index', compact('layanan', 'stats', 'dinasOptions'));
    }

    public function updateStatus(Request $request, LayananPublik $layanan)
    {
        $validated = $request->validate([
            'validation_status' => ['required', 'in:pending,valid,revisi,salah_mapping'],
            'validation_note'   => ['nullable', 'string', 'max:1000'],
        ]);

        $layanan->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Status layanan #{$layanan->nomor} berhasil diperbarui."]);
        }

        return redirect()->route('admin.validasi.index')
            ->with('success', "Status layanan #{$layanan->nomor} berhasil diperbarui.");
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids'               => ['required', 'array'],
            'ids.*'             => ['integer', 'exists:layanan_publik,id'],
            'validation_status' => ['required', 'in:pending,valid,revisi,salah_mapping'],
        ]);

        LayananPublik::whereIn('id', $validated['ids'])
            ->update(['validation_status' => $validated['validation_status']]);

        return redirect()->route('admin.validasi.index')
            ->with('success', count($validated['ids']) . ' data berhasil diperbarui.');
    }
}