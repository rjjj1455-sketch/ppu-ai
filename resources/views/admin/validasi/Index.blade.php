@extends('admin.layouts.app')

@section('title', 'Layanan Publik')
@section('breadcrumb', 'Layanan Publik')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Layanan Publik</div>
        <div class="page-subtitle">Kelola data keluhan & solusi layanan masyarakat PPU</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.layanan.export') }}" class="btn btn-ghost">
            ↓ Export CSV
        </a>
        <a href="{{ route('admin.layanan.create') }}" class="btn btn-primary">
            + Tambah Data
        </a>
    </div>
</div>

<!-- Filter Bar -->
<form method="GET" action="{{ route('admin.layanan.index') }}">
<div class="filter-bar">
    <input type="text"
           name="search"
           class="form-control search-input"
           placeholder="Cari keluhan, solusi, atau dinas..."
           value="{{ request('search') }}">

    <select name="dinas" class="form-control">
        <option value="">Semua Dinas</option>
        @foreach($dinasOptions as $dinas)
            <option value="{{ $dinas }}" {{ request('dinas') == $dinas ? 'selected' : '' }}>
                {{ $dinas }}
            </option>
        @endforeach
    </select>

    <select name="status" class="form-control">
        <option value="">Semua Status</option>
        <option value="pending"       {{ request('status') == 'pending'       ? 'selected' : '' }}>Pending</option>
        <option value="valid"         {{ request('status') == 'valid'         ? 'selected' : '' }}>Valid</option>
        <option value="revisi"        {{ request('status') == 'revisi'        ? 'selected' : '' }}>Revisi</option>
        <option value="salah_mapping" {{ request('status') == 'salah_mapping' ? 'selected' : '' }}>Salah Mapping</option>
    </select>

    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Dari">
    <input type="date" name="date_to"   class="form-control" value="{{ request('date_to') }}"   placeholder="Sampai">

    <button type="submit" class="btn btn-primary">Cari</button>
    <a href="{{ route('admin.layanan.index') }}" class="btn btn-ghost">Reset</a>
</div>
</form>

<!-- Summary badges -->
<div style="display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center;">
    <span style="font-size:12px; color:var(--text3);">Menampilkan {{ $layanan->total() }} data</span>
    @if(request()->hasAny(['search','dinas','status','date_from','date_to']))
        <span class="badge badge-blue">⚡ Filter aktif</span>
    @endif
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>Keluhan</th>
                <th>Dinas</th>
                <th>Instagram</th>
                <th>Status</th>
                <th>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                       style="color:inherit; text-decoration:none;">
                        Tanggal {{ request('sort') == 'created_at' ? (request('direction') == 'asc' ? '↑' : '↓') : '↕' }}
                    </a>
                </th>
                <th style="width:140px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($layanan as $item)
            <tr>
                <td>
                    <div class="text-main">{{ $item->nomor ?? $item->id }}</div>
                </td>
                <td>
                    <div class="text-main truncate" style="max-width:280px;">{{ $item->keluhan }}</div>
                    <div class="text-sub truncate" style="max-width:280px;">{{ Str::limit($item->solusi, 80) }}</div>
                </td>
                <td>
                    @if($item->dinas)
                        <div class="text-main" style="max-width:200px; white-space:normal; font-size:12px;">{{ $item->dinas }}</div>
                    @else
                        <span style="color:var(--text3); font-size:12px;">—</span>
                    @endif
                </td>
                <td>
                    @if($item->instagram)
                        <a href="https://instagram.com/{{ ltrim($item->instagram, '@') }}"
                           target="_blank"
                           style="color:var(--accent2); text-decoration:none; font-size:12px;">
                            {{ $item->instagram }}
                        </a>
                    @else
                        <span style="color:var(--text3); font-size:12px;">—</span>
                    @endif
                </td>
                <td>
                    @php
                        $colors = [
                            'valid'         => 'green',
                            'revisi'        => 'yellow',
                            'salah_mapping' => 'red',
                            'pending'       => 'gray',
                        ];
                        $labels = [
                            'valid'         => 'Valid',
                            'revisi'        => 'Revisi',
                            'salah_mapping' => 'Salah Mapping',
                            'pending'       => 'Pending',
                        ];
                    @endphp
                    <span class="badge badge-{{ $colors[$item->validation_status] ?? 'gray' }}">
                        {{ $labels[$item->validation_status] ?? $item->validation_status }}
                    </span>
                </td>
                <td>
                    <div class="text-main">{{  $item->created_at ? $item->created_at->format('d M Y') : '-' }}</div>
                    <div class="text-sub">{{ $item->created_at ? $item->created_at->format('H:i') : '-' }}</div>
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <a href="{{ route('admin.layanan.edit', $item) }}" class="btn btn-sm btn-ghost" title="Edit">✏</a>
                        <form method="POST" action="{{ route('admin.layanan.destroy', $item) }}"
                              onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">✕</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>Tidak ada data ditemukan</h3>
                        <p style="font-size:13px; margin-top:6px;">
                            @if(request()->hasAny(['search','dinas','status']))
                                Coba ubah filter pencarian Anda.
                            @else
                                Tambah data layanan publik pertama!
                            @endif
                        </p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($layanan->hasPages())
<div style="display:flex; align-items:center; justify-content:space-between; margin-top:20px; flex-wrap:wrap; gap:12px;">
    <span style="font-size:12px; color:var(--text3);">
        Halaman {{ $layanan->currentPage() }} dari {{ $layanan->lastPage() }}
        ({{ $layanan->total() }} total)
    </span>

    <div class="pagination">
        @if($layanan->onFirstPage())
            <span class="page-link disabled">‹</span>
        @else
            <a href="{{ $layanan->previousPageUrl() }}" class="page-link">‹</a>
        @endif

        @foreach($layanan->getUrlRange(max(1, $layanan->currentPage()-2), min($layanan->lastPage(), $layanan->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="page-link {{ $page == $layanan->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach

        @if($layanan->hasMorePages())
            <a href="{{ $layanan->nextPageUrl() }}" class="page-link">›</a>
        @else
            <span class="page-link disabled">›</span>
        @endif
    </div>
</div>
@endif

@endsection