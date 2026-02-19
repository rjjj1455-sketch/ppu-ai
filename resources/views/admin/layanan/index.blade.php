@extends('admin.layouts.app')

@section('title', 'Validasi Data')
@section('breadcrumb', 'Validasi Data')

@push('styles')
<style>
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .strip-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
        display: block;
    }

    .strip-card:hover { border-color: var(--accent); }
    .strip-card.active { border-color: var(--accent); background: rgba(59,130,246,.08); }

    .strip-num   { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: var(--text); }
    .strip-label { font-size: 11px; color: var(--text3); margin-top: 2px; }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.7);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.open { display: flex; }

    .modal {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        max-width: 560px;
        width: 100%;
        margin: 20px;
        animation: slideUp .2s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }

    .modal-title {
        font-family: 'Syne', sans-serif;
        font-size: 18px; font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
    }

    .modal-sub { font-size: 13px; color: var(--text3); margin-bottom: 20px; }

    .status-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 16px;
    }

    .status-btn {
        padding: 12px;
        border-radius: var(--radius-sm);
        border: 2px solid var(--border);
        background: var(--bg3);
        color: var(--text2);
        font-family: 'DM Sans', sans-serif;
        font-size: 13px; font-weight: 600;
        cursor: pointer;
        text-align: center;
        transition: all .2s;
    }
    .status-btn:hover { border-color: var(--accent); color: var(--text); }
    .status-btn.selected { border-color: var(--accent); background: rgba(59,130,246,.15); color: var(--accent); }
    .status-btn[data-status="valid"].selected   { border-color: var(--green);  background: rgba(16,185,129,.15); color: var(--green); }
    .status-btn[data-status="revisi"].selected  { border-color: var(--yellow); background: rgba(245,158,11,.15); color: var(--yellow); }
    .status-btn[data-status="salah_mapping"].selected { border-color: var(--red); background: rgba(239,68,68,.15); color: var(--red); }

    @media (max-width: 900px) {
        .stats-strip { grid-template-columns: repeat(3, 1fr); }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Validasi Data Layanan</div>
        <div class="page-subtitle">Review dan verifikasi data keluhan & solusi yang digenerate AI</div>
    </div>
</div>

<!-- Stats Strip -->
<div class="stats-strip">
    <a href="{{ route('admin.validasi.index') }}"
       class="strip-card {{ !request('status') ? 'active' : '' }}">
        <div class="strip-num">{{ $stats['total'] }}</div>
        <div class="strip-label">Total Data</div>
    </a>
    <a href="{{ route('admin.validasi.index', ['status' => 'pending']) }}"
       class="strip-card {{ request('status') == 'pending' ? 'active' : '' }}">
        <div class="strip-num" style="color:var(--text3);">{{ $stats['pending'] }}</div>
        <div class="strip-label">Pending</div>
    </a>
    <a href="{{ route('admin.validasi.index', ['status' => 'valid']) }}"
       class="strip-card {{ request('status') == 'valid' ? 'active' : '' }}">
        <div class="strip-num" style="color:var(--green);">{{ $stats['valid'] }}</div>
        <div class="strip-label">Valid</div>
    </a>
    <a href="{{ route('admin.validasi.index', ['status' => 'revisi']) }}"
       class="strip-card {{ request('status') == 'revisi' ? 'active' : '' }}">
        <div class="strip-num" style="color:var(--yellow);">{{ $stats['revisi'] }}</div>
        <div class="strip-label">Revisi</div>
    </a>
    <a href="{{ route('admin.validasi.index', ['status' => 'salah_mapping']) }}"
       class="strip-card {{ request('status') == 'salah_mapping' ? 'active' : '' }}">
        <div class="strip-num" style="color:var(--red);">{{ $stats['salah'] }}</div>
        <div class="strip-label">Salah Mapping</div>
    </a>
</div>

<!-- Filters -->
<form method="GET">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <div class="filter-bar">
        <input type="text" name="search" class="form-control search-input"
               placeholder="Cari keluhan atau dinas..."
               value="{{ request('search') }}">

        <select name="dinas" class="form-control">
            <option value="">Semua Dinas</option>
            @foreach($dinasOptions as $dinas)
                <option value="{{ $dinas }}" {{ request('dinas') == $dinas ? 'selected' : '' }}>
                    {{ $dinas }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('admin.validasi.index') }}" class="btn btn-ghost">Reset</a>
    </div>
</form>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:50px">#</th>
                <th>Keluhan</th>
                <th>Solusi AI</th>
                <th>Dinas</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th style="width:120px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($layanan as $item)
            <tr>
                <td>
                    <div class="text-main" style="font-size:12px;">{{ $item->nomor ?? $item->id }}</div>
                </td>
                <td style="max-width:220px;">
                    <div class="truncate text-main" style="max-width:220px;" title="{{ $item->keluhan }}">
                        {{ $item->keluhan }}
                    </div>
                    @if($item->instagram)
                        <div class="text-sub">{{ $item->instagram }}</div>
                    @endif
                </td>
                <td style="max-width:240px;">
                    <div class="truncate" style="max-width:240px; font-size:12px; color:var(--text2);" title="{{ $item->solusi }}">
                        {{ Str::limit($item->solusi, 100) }}
                    </div>
                    @if($item->validation_note)
                        <div class="text-sub" style="color:var(--yellow);">📝 {{ Str::limit($item->validation_note, 50) }}</div>
                    @endif
                </td>
                <td>
                    <div style="font-size:12px; color:var(--text2); max-width:160px; white-space:normal;">
                        {{ $item->dinas ?? '—' }}
                    </div>
                </td>
                <td>
                    @php
                        $colors = ['valid' => 'green', 'revisi' => 'yellow', 'salah_mapping' => 'red', 'pending' => 'gray'];
                        $labels = ['valid' => 'Valid', 'revisi' => 'Revisi', 'salah_mapping' => 'Salah Mapping', 'pending' => 'Pending'];
                    @endphp
                    <span class="badge badge-{{ $colors[$item->validation_status] ?? 'gray' }}">
                        {{ $labels[$item->validation_status] ?? $item->validation_status }}
                    </span>
                </td>
                <td>
                    <div class="text-main" style="font-size:12px;">{{ $item->created_at->format('d M Y') }}</div>
                    <div class="text-sub">{{ $item->created_at->diffForHumans() }}</div>
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button onclick="openModal({{ $item->id }}, '{{ addslashes(Str::limit($item->keluhan, 60)) }}', '{{ $item->validation_status }}')"
                                class="btn btn-sm btn-primary" title="Update Status">
                            ◎ Review
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <div class="empty-icon">✓</div>
                        <h3>
                            @if(request('status') == 'pending')
                                Tidak ada data pending!
                            @else
                                Tidak ada data ditemukan
                            @endif
                        </h3>
                        <p style="font-size:13px; margin-top:6px; color:var(--text3);">
                            @if(request('status') == 'pending')
                                Semua data telah divalidasi. Kerja bagus! 🎉
                            @else
                                Coba ubah filter pencarian.
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
        Halaman {{ $layanan->currentPage() }} dari {{ $layanan->lastPage() }} ({{ $layanan->total() }} total)
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

<!-- ── Modal Validasi ── -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal">
        <div class="modal-title">Review Data #<span id="modalId"></span></div>
        <div class="modal-sub" id="modalKeluhan"></div>

        <form id="validasiForm" method="POST">
            @csrf
            @method('PATCH')

            <div style="font-size:12px; font-weight:600; color:var(--text3); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">
                Pilih Status
            </div>

            <div class="status-options">
                <button type="button" class="status-btn" data-status="valid"         onclick="selectStatus(this)">✓ Valid</button>
                <button type="button" class="status-btn" data-status="revisi"        onclick="selectStatus(this)">↩ Revisi</button>
                <button type="button" class="status-btn" data-status="pending"       onclick="selectStatus(this)">⏳ Pending</button>
                <button type="button" class="status-btn" data-status="salah_mapping" onclick="selectStatus(this)">✕ Salah Mapping</button>
            </div>

            <input type="hidden" name="validation_status" id="selectedStatus">

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Catatan Admin (Opsional)</label>
                <textarea name="validation_note" id="modalNote" class="form-control" rows="3"
                          placeholder="Tulis catatan atau alasan perubahan status..."></textarea>
            </div>

            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Simpan Status</button>
                <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentLayananId = null;

function openModal(id, keluhan, currentStatus) {
    currentLayananId = id;
    document.getElementById('modalId').textContent = id;
    document.getElementById('modalKeluhan').textContent = '"' + keluhan + '"';
    document.getElementById('modalNote').value = '';
    document.getElementById('selectedStatus').value = '';
    document.getElementById('submitBtn').disabled = true;

    // Set form action
    document.getElementById('validasiForm').action = `/admin/validasi/${id}/status`;

    // Reset buttons
    document.querySelectorAll('.status-btn').forEach(btn => btn.classList.remove('selected'));

    // Pre-select current status
    const current = document.querySelector(`[data-status="${currentStatus}"]`);
    if (current) {
        current.classList.add('selected');
        document.getElementById('selectedStatus').value = currentStatus;
        document.getElementById('submitBtn').disabled = false;
    }

    document.getElementById('modalOverlay').classList.add('open');
}

function selectStatus(btn) {
    document.querySelectorAll('.status-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('selectedStatus').value = btn.dataset.status;
    document.getElementById('submitBtn').disabled = false;
}

function closeModal() {
    document.getElementById('modalOverlay').classList.remove('open');
}

function closeModalOutside(event) {
    if (event.target === document.getElementById('modalOverlay')) closeModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>
@endpush