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

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('dinas')) {
            $query->filterDinas($request->dinas);
        }

        if ($request->filled('status')) {
            $query->where('validation_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sortBy  = in_array($request->get('sort'), ['nomor', 'dinas', 'created_at', 'validation_status'])
            ? $request->get('sort') : 'created_at';
        $sortDir = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDir);

        $layanan = $query->paginate(15)->withQueryString();

        $dinasOptions = LayananPublik::select('dinas')
            ->whereNotNull('dinas')
            ->where('dinas', '!=', '')
            ->distinct()
            ->orderBy('dinas')
            ->pluck('dinas');

        $stats = [
            'total'   => LayananPublik::count(),
            'pending' => LayananPublik::where('validation_status', 'pending')->count(),
            'valid'   => LayananPublik::where('validation_status', 'valid')->count(),
            'revisi'  => LayananPublik::where('validation_status', 'revisi')->count(),
            'salah'   => LayananPublik::where('validation_status', 'salah_mapping')->count(),
        ];

        return view('admin.layanan.index', compact('layanan', 'dinasOptions', 'stats'));
    }

    public function create()
    {
        return view('admin.layanan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keluhan'           => ['required', 'string'],
            'solusi'            => ['required', 'string'],
            'dinas'             => ['nullable', 'string', 'max:255'],
            'link'              => ['nullable', 'url', 'max:2048'],
            'instagram'         => ['nullable', 'string', 'max:255'],
            'validation_status' => ['nullable', 'in:pending,valid,revisi,salah_mapping'],
        ]);

        $validated['nomor'] = (LayananPublik::max('nomor') ?? 0) + 1;

        LayananPublik::create($validated);

        return redirect()->route('admin.layanan.index')
            ->with('success', 'Data layanan publik berhasil ditambahkan.');
    }

    public function show(LayananPublik $layanan)
    {
        return view('admin.layanan.show', compact('layanan'));
    }

    public function edit(LayananPublik $layanan)
    {
        return view('admin.layanan.edit', compact('layanan'));
    }

    public function update(Request $request, LayananPublik $layanan)
    {
        $validated = $request->validate([
            'keluhan'           => ['required', 'string'],
            'solusi'            => ['required', 'string'],
            'dinas'             => ['nullable', 'string', 'max:255'],
            'link'              => ['nullable', 'url', 'max:2048'],
            'instagram'         => ['nullable', 'string', 'max:255'],
            'validation_status' => ['nullable', 'in:pending,valid,revisi,salah_mapping'],
            'validation_note'   => ['nullable', 'string', 'max:1000'],
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
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Nomor', 'Keluhan', 'Solusi', 'Dinas', 'Link', 'Instagram', 'Status Validasi', 'Catatan', 'Tanggal']);

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
                        $item->validation_note,
                        $item->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }
}