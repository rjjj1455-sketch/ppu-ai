@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-container {
        position: relative;
        height: 240px;
    }

    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .list-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .list-item:last-child { border-bottom: none; }

    .list-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        background: var(--bg3);
    }

    .list-body { flex: 1; min-width: 0; }
    .list-title { font-size: 13px; font-weight: 500; color: var(--text); }
    .list-sub   { font-size: 11px; color: var(--text3); margin-top: 2px; }

    .dinas-bar-row {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 10px;
    }
    .dinas-label { font-size: 12px; color: var(--text2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
    .dinas-count { font-size: 12px; font-weight: 700; color: var(--text); min-width: 28px; text-align: right; }
    .dinas-bar-track { width: 80px; height: 5px; background: var(--bg3); border-radius: 10px; }
    .dinas-bar-fill  { height: 5px; background: linear-gradient(90deg, var(--accent), var(--accent2)); border-radius: 10px; transition: width .6s ease; }

    .today-vs {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: var(--text3);
        margin-top: 6px;
    }
    .today-up   { color: var(--green); font-weight: 700; }
    .today-down { color: var(--red);   font-weight: 700; }

    @media (max-width: 1024px) {
        .charts-grid { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Dashboard Overview</div>
        <div class="page-subtitle">Ringkasan aktivitas sistem PPU AI — {{ now()->translatedFormat('l, d F Y') }}</div>
    </div>
    <a href="{{ route('admin.validasi.index') }}" class="btn btn-primary">
        ◎ Validasi Data
    </a>
</div>

<!-- ── Stat Cards ── -->
<div class="stats-grid">
    <div class="stat-card" style="--accent-color: var(--accent)">
        <span class="stat-icon">💬</span>
        <div class="stat-label">Total Chat</div>
        <div class="stat-value">{{ number_format($totalChats) }}</div>
        <div class="stat-sub">Semua percakapan masuk</div>
        <div class="today-vs">
            Hari ini:
            @if($chatsToday > $chatsYesterday)
                <span class="today-up">▲ {{ $chatsToday }}</span>
            @elseif($chatsToday < $chatsYesterday)
                <span class="today-down">▼ {{ $chatsToday }}</span>
            @else
                <span>{{ $chatsToday }}</span>
            @endif
            <span>vs kemarin {{ $chatsYesterday }}</span>
        </div>
    </div>

    <div class="stat-card" style="--accent-color: var(--accent2)">
        <span class="stat-icon">📋</span>
        <div class="stat-label">Layanan Publik</div>
        <div class="stat-value">{{ number_format($totalLayanan) }}</div>
        <div class="stat-sub">Data keluhan & solusi tersimpan</div>
    </div>

    <div class="stat-card" style="--accent-color: var(--green)">
        <span class="stat-icon">✓</span>
        <div class="stat-label">Tervalidasi</div>
        <div class="stat-value">{{ number_format($totalValid) }}</div>
        <div class="stat-sub">
            @if($totalLayanan > 0)
                {{ round($totalValid / $totalLayanan * 100) }}% dari total data
            @else
                0% dari total data
            @endif
        </div>
    </div>

    <div class="stat-card" style="--accent-color: var(--yellow)">
        <span class="stat-icon">⏳</span>
        <div class="stat-label">Pending Validasi</div>
        <div class="stat-value">{{ number_format($totalPending) }}</div>
        <div class="stat-sub">Menunggu review admin</div>
    </div>

    <div class="stat-card" style="--accent-color: var(--accent3)">
        <span class="stat-icon">↩</span>
        <div class="stat-label">Perlu Revisi</div>
        <div class="stat-value">{{ number_format($totalRevisi) }}</div>
        <div class="stat-sub">Data butuh perbaikan</div>
    </div>

    <div class="stat-card" style="--accent-color: var(--red)">
        <span class="stat-icon">✕</span>
        <div class="stat-label">Salah Mapping</div>
        <div class="stat-value">{{ number_format($totalSalah) }}</div>
        <div class="stat-sub">Terdeteksi mapping keliru</div>
    </div>
</div>

<!-- ── Charts ── -->
<div class="charts-grid">
    <!-- Line chart: chat per hari -->
    <div class="card">
        <div class="card-title">Aktivitas Chat — 7 Hari Terakhir</div>
        <div class="chart-container">
            <canvas id="chatChart"></canvas>
        </div>
    </div>

    <!-- Dinas bar chart -->
    <div class="card">
        <div class="card-title">Dinas Terpopuler</div>
        @forelse($dinasStats as $stat)
            @php $maxJumlah = $dinasStats->max('jumlah'); @endphp
            <div class="dinas-bar-row">
                <div class="dinas-label" title="{{ $stat->dinas }}">{{ Str::limit($stat->dinas, 28) }}</div>
                <div class="dinas-bar-track">
                    <div class="dinas-bar-fill" style="width: {{ $maxJumlah > 0 ? ($stat->jumlah / $maxJumlah * 100) : 0 }}%"></div>
                </div>
                <div class="dinas-count">{{ $stat->jumlah }}</div>
            </div>
        @empty
            <div style="color: var(--text3); font-size: 13px; text-align: center; padding: 30px 0;">Belum ada data dinas</div>
        @endforelse
    </div>
</div>

<!-- ── Recent Data ── -->
<div class="bottom-grid">
    <!-- Recent Chats -->
    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <div class="card-title" style="margin-bottom:0">Chat Terbaru</div>
            <span style="font-size:11px; color:var(--text3);">5 terakhir</span>
        </div>

        @forelse($recentChats as $chat)
        <div class="list-item">
            <div class="list-icon">💬</div>
            <div class="list-body">
                <div class="list-title truncate">{{ $chat->user_message }}</div>
                <div class="list-sub" style="display:flex; gap:8px; align-items:center;">
                    <span>{{ $chat->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding:30px">
            <div class="empty-icon">💬</div>
            <p>Belum ada chat</p>
        </div>
        @endforelse
    </div>

    <!-- Pending Layanan -->
    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <div class="card-title" style="margin-bottom:0">Menunggu Validasi</div>
            <a href="{{ route('admin.validasi.index', ['status' => 'pending']) }}" class="btn btn-sm btn-ghost">Lihat Semua</a>
        </div>

        @forelse($pendingLayanan as $item)
        <div class="list-item">
            <div class="list-icon">📋</div>
            <div class="list-body">
                <div class="list-title truncate">{{ $item->keluhan }}</div>
                <div class="list-sub" style="display:flex; gap:8px; align-items:center;">
                    <span class="badge badge-gray">{{ $item->dinas ?? 'Dinas tidak diketahui' }}</span>
                </div>
            </div>
            <a href="{{ route('admin.validasi.index', ['search' => $item->id]) }}" class="btn btn-sm btn-ghost" style="flex-shrink:0;">→</a>
        </div>
        @empty
        <div class="empty-state" style="padding:30px">
            <div class="empty-icon">✓</div>
            <h3>Semua data tervalidasi!</h3>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('chatChart').getContext('2d');

const gradient = ctx.createLinearGradient(0, 0, 0, 240);
gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Jumlah Chat',
            data: {!! json_encode($chartData) !!},
            borderColor: '#3b82f6',
            backgroundColor: gradient,
            borderWidth: 2.5,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#111827',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a2235',
                borderColor: '#1e2d4a',
                borderWidth: 1,
                titleColor: '#e2e8f0',
                bodyColor: '#94a3b8',
                padding: 10,
                callbacks: {
                    title: (items) => items[0].label,
                    label: (item) => ` ${item.raw} percakapan`,
                }
            }
        },
        scales: {
            x: {
                grid: { color: '#1e2d4a', drawBorder: false },
                ticks: { color: '#64748b', font: { size: 11 } }
            },
            y: {
                grid: { color: '#1e2d4a', drawBorder: false },
                ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1 },
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush