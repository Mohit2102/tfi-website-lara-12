<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') — TFI Portal</title>
    <meta name="description" content="@yield('meta_description', 'TFI portal dashboard')" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:   @yield('accent', '#7c3aed');
            --accent-l: @yield('accent-l', '#8b5cf6');
            --accent-d: @yield('accent-d', '#5b21b6');
            --dark:     #0b0b15;
            --dark2:    #111120;
            --sidebar:  #0d0d1f;
            --card:     rgba(255,255,255,0.04);
            --card-h:   rgba(255,255,255,0.07);
            --border:   rgba(255,255,255,0.08);
            --text:     #e2e8f0;
            --muted:    #64748b;
            --radius:   14px;
        }

        html, body { min-height: 100vh; font-family: 'Inter', sans-serif; background: var(--dark); color: var(--text); }

        /* ── Layout ── */
        .layout { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.3s;
        }

        .sidebar-logo {
            padding: 1.75rem 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, @yield('accent', '#7c3aed'), @yield('accent-l', '#8b5cf6'));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(124,58,237,0.4);
        }

        .sidebar-logo-text h2 {
            font-size: 1rem; font-weight: 800;
            background: linear-gradient(135deg, #fff, @yield('accent-l', '#8b5cf6'));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        .sidebar-logo-text span {
            font-size: 0.7rem; color: var(--muted); font-weight: 500; letter-spacing: 0.05em; text-transform: uppercase;
        }

        .sidebar-user {
            padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, @yield('accent', '#7c3aed'), @yield('accent-l', '#8b5cf6'));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem; color: #fff;
            flex-shrink: 0;
        }

        .user-info .user-name { font-size: 0.85rem; font-weight: 600; color: var(--text); }
        .user-info .user-role {
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
            color: @yield('accent-l', '#8b5cf6');
        }

        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%; background: #10b981;
            margin-left: auto; flex-shrink: 0;
            box-shadow: 0 0 6px #10b981;
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

        .sidebar-nav { padding: 1rem 0.75rem; flex: 1; overflow-y: auto; }
        .nav-section-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--muted); padding: 0 0.75rem; margin: 1rem 0 0.5rem;
        }

        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            color: var(--muted); text-decoration: none;
            font-size: 0.875rem; font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,0.05);
            color: var(--text);
        }

        .nav-item.active {
            background: linear-gradient(90deg, rgba(124,58,237,0.15), rgba(124,58,237,0.05));
            border-left: 3px solid @yield('accent', '#7c3aed');
            color: #fff;
        }

        .nav-icon { font-size: 1rem; width: 20px; text-align: center; }

        .sidebar-bottom {
            padding: 1rem 0.75rem;
            border-top: 1px solid var(--border);
        }

        /* ── Main ── */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Top bar ── */
        .topbar {
            height: 64px;
            background: rgba(11,11,21,0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            position: sticky; top: 0; z-index: 50;
        }

        .topbar-left h1 { font-size: 1.1rem; font-weight: 700; }
        .topbar-left p { font-size: 0.78rem; color: var(--muted); }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }

        .topbar-btn {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem; font-weight: 600;
            cursor: pointer; transition: all 0.2s;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--muted);
        }

        .topbar-btn:hover { background: var(--card-h); color: var(--text); }

        .logout-btn {
            border-color: rgba(239,68,68,0.3);
            color: #fca5a5;
            background: rgba(239,68,68,0.05);
        }
        .logout-btn:hover { background: rgba(239,68,68,0.1); color: #f87171; }

        /* ── Content ── */
        .content { padding: 2rem; flex: 1; }

        /* ── Flash alert ── */
        .flash {
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: flex; align-items: center; gap: 0.5rem;
            animation: slideDown 0.4s ease;
        }
        @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
        .flash-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; }
        .flash-error   { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.25);  color: #fca5a5; }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            cursor: default;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, @yield('accent', '#7c3aed'), @yield('accent-l', '#8b5cf6'));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stat-card:hover { border-color: rgba(255,255,255,0.15); background: var(--card-h); transform: translateY(-2px); }
        .stat-card:hover::before { opacity: 1; }

        .stat-icon {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .stat-value {
            font-size: 2rem; font-weight: 800;
            background: linear-gradient(135deg, #fff, @yield('accent-l', '#8b5cf6'));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            line-height: 1;
            margin-bottom: 0.4rem;
        }

        .stat-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; }

        .stat-change {
            position: absolute; top: 1rem; right: 1rem;
            font-size: 0.7rem; font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 100px;
        }

        .stat-change.up   { background: rgba(16,185,129,0.15); color: #34d399; }
        .stat-change.down { background: rgba(239,68,68,0.15);  color: #f87171; }

        /* ── Section header ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1rem;
        }
        .section-title { font-size: 1rem; font-weight: 700; }
        .section-action {
            font-size: 0.8rem; color: @yield('accent-l','#8b5cf6');
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .section-action:hover { opacity: 0.7; }

        /* ── Table ── */
        .table-wrap {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            padding: 0.875rem 1rem;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
        }

        td {
            padding: 0.875rem 1rem;
            font-size: 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        .td-name { display: flex; align-items: center; gap: 0.75rem; }
        .td-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 700;
            flex-shrink: 0;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;
        }

        .badge-superadmin { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); }
        .badge-admin      { background: rgba(14,165,233,0.15);  color: #38bdf8; border: 1px solid rgba(14,165,233,0.3); }
        .badge-customer   { background: rgba(16,185,129,0.15);  color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
        .badge-active     { background: rgba(16,185,129,0.12);  color: #34d399; }
        .badge-inactive   { background: rgba(239,68,68,0.12);   color: #f87171; }

        /* ── Welcome banner ── */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(14,165,233,0.08));
            border: 1px solid rgba(124,58,237,0.2);
            border-radius: var(--radius);
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            display: flex; align-items: center; gap: 1.5rem;
            position: relative; overflow: hidden;
        }

        .welcome-banner::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.02));
        }

        .welcome-emoji { font-size: 3rem; }
        .welcome-text h2 { font-size: 1.3rem; font-weight: 800; margin-bottom: 0.25rem; }
        .welcome-text p  { font-size: 0.85rem; color: var(--muted); }

        /* ── Quick actions ── */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
            color: var(--text);
        }

        .action-card:hover {
            border-color: @yield('accent-l','#8b5cf6');
            background: rgba(124,58,237,0.08);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .action-icon { font-size: 1.75rem; margin-bottom: 0.5rem; display: block; }
        .action-label { font-size: 0.8rem; font-weight: 600; color: var(--muted); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
    @yield('extra_styles')
</head>
<body>
<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🏋️</div>
            <div class="sidebar-logo-text">
                <h2>TFI Portal</h2>
                <span>@yield('role_label', 'Dashboard')</span>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <span class="status-dot"></span>
        </div>

        <nav class="sidebar-nav">
            @yield('nav_items')
        </nav>

        <div class="sidebar-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; color: #fca5a5;">
                    <span class="nav-icon">🚪</span> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <h1>@yield('page_title', 'Dashboard')</h1>
                <p>{{ \App\Support\DashboardGreeting::today() }}</p>
            </div>
            <div class="topbar-right">
                <span class="topbar-btn">🔔</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="topbar-btn logout-btn">🚪 Logout</button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <div class="content">
            @if (session('success'))
                <div class="flash flash-success">✓ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">✕ {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script>
    // Sidebar mobile toggle
    const sidebar = document.getElementById('sidebar');
</script>
@yield('scripts')
</body>
</html>
