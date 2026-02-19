<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayananPublik;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LayananPublikController extends Controller
{
    public function index(Request $request)
    {
        $query = LayananPublik::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter dinas
        if ($request->filled('dinas')) {
            $query->filterDinas($request->dinas);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort', 'nomor');
        $sortDir = $request->get('direction', 'desc');
        $allowedSorts = ['nomor', 'dinas', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $layanan = $query->paginate(15)->withQueryString();

        // Daftar dinas untuk filter dropdown
        $dinasOptions = LayananPublik::select('dinas')
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->distinct()
            ->orderBy('dinas')
            ->pluck('dinas');

        return view('admin.layanan.index', compact('layanan', 'dinasOptions'));
    }

    public function create()
    {
        return view('admin.layanan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keluhan'   => ['required', 'string'],
            'solusi'    => ['required', 'string'],
            'dinas'     => ['nullable', 'string', 'max:255'],
            'link'      => ['nullable', 'url', 'max:2048'],
            'instagram' => ['nullable', 'string', 'max:255'],
        ]);

        LayananPublik::create($validated);

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Data layanan publik berhasil ditambahkan.');
    }

    public function edit(LayananPublik $layanan)
    {
        return view('admin.layanan.edit', compact('layanan'));
    }

    public function update(Request $request, LayananPublik $layanan)
    {
        $validated = $request->validate([
            'keluhan'   => ['required', 'string'],
            'solusi'    => ['required', 'string'],
            'dinas'     => ['nullable', 'string', 'max:255'],
            'link'      => ['nullable', 'url', 'max:2048'],
            'instagram' => ['nullable', 'string', 'max:255'],
        ]);

        $layanan->update($validated);

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Data layanan publik berhasil diperbarui.');
    }

    public function destroy(LayananPublik $layanan)
    {
        $layanan->delete();

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Data layanan publik berhasil dihapus.');
    }

    public function exportCsv()
    {
        $filename = 'layanan_publik_' . date('Y-m-d_His') . '.csv';

        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, ['Nomor', 'Keluhan', 'Solusi', 'Dinas', 'Link', 'Instagram', 'Status Validasi', 'Tanggal']);

            // Data
            LayananPublik::orderBy('nomor')->chunk(200, function ($items) use ($handle) {
                foreach ($items as $item) {
                    fputcsv($handle, [
                        $item->nomor,
                        $item->keluhan,
                        $item->solusi,
                        $item->dinas,
                        $item->link,
                        $item->instagram,
                        $item->validation_status,
                        $item->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }
}
