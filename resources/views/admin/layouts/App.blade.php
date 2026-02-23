<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PPU AI Admin</title>
    
    <!-- Google Fonts: Syne (display) + DM Sans (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg:         #0a0f1e;
            --bg2:        #111827;
            --bg3:        #1a2235;
            --border:     #1e2d4a;
            --accent:     #3b82f6;
            --accent2:    #06b6d4;
            --accent3:    #8b5cf6;
            --green:      #10b981;
            --yellow:     #f59e0b;
            --red:        #ef4444;
            --gray:       #6b7280;
            --text:       #e2e8f0;
            --text2:      #94a3b8;
            --text3:      #64748b;
            --sidebar-w:  260px;
            --radius:     12px;
            --radius-sm:  8px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg2); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

        /* ══════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent3));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800;
            font-family: 'Syne', sans-serif;
            color: white;
            flex-shrink: 0;
        }

        .logo-text {
            font-family: 'Syne', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 10px; font-weight: 400;
            color: var(--text3);
            font-family: 'DM Sans', sans-serif;
            letter-spacing: .5px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }

        .nav-section-label {
            font-size: 10px; font-weight: 600;
            color: var(--text3);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 8px 8px 4px;
            margin-bottom: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text2);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all .2s;
            position: relative;
        }

        .nav-item:hover {
            background: var(--bg3);
            color: var(--text);
        }

        .nav-item.active {
            background: rgba(59, 130, 246, .15);
            color: var(--accent);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-icon { font-size: 16px; width: 20px; text-align: center; }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: white;
            font-size: 10px; font-weight: 700;
            padding: 1px 7px;
            border-radius: 20px;
            min-width: 20px;
            text-align: center;
        }

        .nav-badge.yellow { background: var(--yellow); color: #1a2235; }
        .nav-badge.red    { background: var(--red); }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), var(--accent3));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
            color: white; flex-shrink: 0;
        }

        .footer-info { flex: 1; min-width: 0; }
        .footer-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .footer-role { font-size: 11px; color: var(--text3); }

        .logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text3);
            font-size: 18px;
            padding: 4px;
            border-radius: 6px;
            transition: color .2s;
            line-height: 1;
        }
        .logout-btn:hover { color: var(--red); }

        /* ══════════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════════ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            height: 60px;
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky; top: 0; z-index: 50;
        }

        .topbar-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px; color: var(--text2);
        }

        .topbar-breadcrumb .current { color: var(--text); font-weight: 600; }
        .topbar-breadcrumb .sep { color: var(--text3); }

        .topbar-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 12px;
        }

        .topbar-time {
            font-size: 12px;
            color: var(--text3);
            font-variant-numeric: tabular-nums;
        }

        .topbar-dot {
            width: 8px; height: 8px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(16,185,129,.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(16,185,129,.2); }
            50%       { box-shadow: 0 0 0 6px rgba(16,185,129,.05); }
        }

        /* ── Page Content ── */
        .content {
            flex: 1;
            padding: 28px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--text2);
            margin-top: 4px;
        }

        /* ══════════════════════════════════════════
           CARDS & UTILITIES
        ══════════════════════════════════════════ */
        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
        }

        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .2s, border-color .2s;
        }

        .stat-card:hover { transform: translateY(-2px); border-color: var(--accent); }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent-color, var(--accent));
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .stat-label { font-size: 11px; font-weight: 600; color: var(--text3); text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800; color: var(--text); margin: 8px 0 4px; line-height: 1; }
        .stat-sub   { font-size: 12px; color: var(--text3); }
        .stat-icon  { position: absolute; right: 16px; top: 16px; font-size: 26px; opacity: .15; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: capitalize;
            letter-spacing: .3px;
        }

        .badge-green  { background: rgba(16,185,129,.15);  color: #10b981; }
        .badge-yellow { background: rgba(245,158,11,.15);  color: #f59e0b; }
        .badge-red    { background: rgba(239,68,68,.15);   color: #ef4444; }
        .badge-gray   { background: rgba(107,114,128,.15); color: #9ca3af; }
        .badge-blue   { background: rgba(59,130,246,.15);  color: #3b82f6; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 600;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all .2s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #2563eb; }

        .btn-danger { background: rgba(239,68,68,.15); color: var(--red); }
        .btn-danger:hover { background: rgba(239,68,68,.25); }

        .btn-ghost { background: transparent; color: var(--text2); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg3); color: var(--text); }

        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-success { background: rgba(16,185,129,.15); color: var(--green); }
        .btn-success:hover { background: rgba(16,185,129,.25); }
        .btn-warning { background: rgba(245,158,11,.15); color: var(--yellow); }
        .btn-warning:hover { background: rgba(245,158,11,.25); }

        /* Tables */
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        table { width: 100%; border-collapse: collapse; }

        thead { background: var(--bg3); }
        thead th {
            padding: 12px 16px;
            font-size: 11px; font-weight: 700;
            color: var(--text3);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            white-space: nowrap;
        }

        tbody tr { border-top: 1px solid var(--border); transition: background .15s; }
        tbody tr:hover { background: var(--bg3); }

        tbody td {
            padding: 12px 16px;
            font-size: 13px;
            color: var(--text2);
            vertical-align: middle;
        }

        td .text-main { color: var(--text); font-weight: 500; }
        td .text-sub  { font-size: 11px; color: var(--text3); margin-top: 2px; }

        /* Forms */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text2); margin-bottom: 6px; letter-spacing: .3px; }

        .form-control {
            width: 100%;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            color: var(--text);
            font-size: 13.5px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .2s;
            outline: none;
        }

        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
        .form-control::placeholder { color: var(--text3); }

        textarea.form-control { resize: vertical; min-height: 100px; }
        select.form-control { cursor: pointer; }

        .form-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }

        /* Alerts / Flash */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }

        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #10b981; }
        .alert-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.3);  color: #ef4444; }

        /* Pagination */
        .pagination { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }

        .pagination .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--bg2);
            color: var(--text2);
            text-decoration: none;
            font-size: 13px;
            transition: all .2s;
        }

        .pagination .page-link:hover { background: var(--bg3); color: var(--text); }
        .pagination .page-link.active { background: var(--accent); border-color: var(--accent); color: white; font-weight: 700; }
        .pagination .page-link.disabled { opacity: .4; pointer-events: none; }

        /* Filter bar */
        .filter-bar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-bar .form-control { width: auto; }
        .search-input { min-width: 260px; }

        /* Truncate */
        .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text3); }
        .empty-state .empty-icon { font-size: 48px; margin-bottom: 12px; opacity: .4; }
        .empty-state h3 { font-family: 'Syne', sans-serif; font-size: 16px; color: var(--text2); margin-bottom: 6px; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .content { padding: 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">P</div>
        <div>
            <div class="logo-text">PPU AI Admin</div>
            <div class="logo-sub">Penajam Paser Utara</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon">⬡</span> Dashboard
        </a>

        <div class="nav-section-label" style="margin-top:16px">Manajemen Data</div>

        <a href="{{ route('admin.layanan.index') }}"
           class="nav-item {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
            <span class="nav-icon">◈</span> Layanan Publik
            @php $totalLayanan = \App\Models\LayananPublik::count(); @endphp
            @if($totalLayanan > 0)
                <span class="nav-badge">{{ $totalLayanan }}</span>
            @endif
        </a>

        <a href="{{ route('admin.validasi.index') }}"
           class="nav-item {{ request()->routeIs('admin.validasi.*') ? 'active' : '' }}">
            <span class="nav-icon">◎</span> Validasi Data
            @php $pending = \App\Models\LayananPublik::where('validation_status','pending')->count(); @endphp
            @if($pending > 0)
                <span class="nav-badge yellow">{{ $pending }}</span>
            @endif
        </a>

        <div class="nav-section-label" style="margin-top:16px">Lainnya</div>

        <a href="{{ route('chat.index') }}" class="nav-item" target="_blank">
            <span class="nav-icon">◌</span> Buka Chat PPU AI
        </a>

        <a href="{{ route('admin.layanan.export') }}" class="nav-item">
            <span class="nav-icon">↓</span> Export CSV
        </a>
    </nav>

    <!-- Sidebar Footer dengan tombol logout -->
    <div class="sidebar-footer">
        <div class="avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="footer-info">
            <div class="footer-name">{{ Auth::user()->name }}</div>
            <div class="footer-role">{{ ucfirst(Auth::user()->role) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">⏻</button>
        </form>
    </div>
</aside>

<!-- ══ MAIN ═══════════════════════════════════════════════════ -->
<div class="main">
    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-breadcrumb">
            <span>Admin</span>
            <span class="sep">/</span>
            <span class="current">@yield('breadcrumb', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span class="topbar-time" id="clock"></span>
            <div class="topbar-dot" title="Sistem Online"></div>
        </div>
    </header>

    <!-- Content -->
    <main class="content">

        @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ✕ {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    // Live clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

@stack('scripts')
</body>
</html>