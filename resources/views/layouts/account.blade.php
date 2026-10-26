@php
    $websiteUrl = rtrim(url('/'), '/').'/';
    $me = auth()->user();
    $isTrainee = $me->isCustomer();
    $dashboardRoute = $isTrainee ? 'customer.dashboard' : 'admin.dashboard';
    $roleLabel = $isTrainee ? 'Trainee' : 'Trainer';
    $initials = collect(preg_split('/\s+/', trim($me->name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>@yield('title', 'Profile') — TFI Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet" />
    <script>
        try { if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark-mode'); } catch (e) {}
    </script>
    <style>
        :root {
            --bg: #f7f4ed; --surface: #ffffff; --gold: #c99a2e; --gold-light: #e2bd5b;
            --gold-bright: #f3d98b; --gold-deep: #8f6819; --text: #17130a; --text-muted: #5b5240;
            --line: rgba(23, 19, 10, 0.10); --line-strong: rgba(23, 19, 10, 0.18);
            --primary-glow: rgba(201, 154, 46, 0.30); --header-bg: rgba(247, 244, 237, 0.86);
            --radius-md: 10px; --radius-lg: 18px; --gold-text: #8f6819; color-scheme: light;
        }
        html.dark-mode {
            --bg: #090806; --surface: #12100d; --gold: #d4a83a; --gold-light: #e8c765;
            --gold-bright: #f7df9a; --gold-deep: #a8791c; --text: #f5f0df; --text-muted: #b9b2a0;
            --line: rgba(245, 240, 223, 0.10); --line-strong: rgba(245, 240, 223, 0.20);
            --primary-glow: rgba(212, 168, 58, 0.34); --header-bg: rgba(9, 8, 6, 0.86);
            --gold-text: #e8c765; color-scheme: dark;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--text); font-family: "Inter", system-ui, sans-serif; font-size: 15px; }
        h1, h2, h3 { font-family: "Oswald", sans-serif; font-weight: 600; letter-spacing: .01em; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; color: inherit; cursor: pointer; border: none; background: none; }
        .ic { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; flex: none; }
        .app { display: grid; grid-template-columns: 276px minmax(0, 1fr); min-height: 100vh; }
        .sidebar {
            position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; padding: 1.4rem 1.1rem;
            background: radial-gradient(120% 40% at 0% 0%, rgba(212,168,58,.20), transparent 60%), linear-gradient(180deg, #100d07 0%, #090806 100%);
            color: #f5f0df; border-right: 1px solid rgba(243,217,139,.14); z-index: 50;
        }
        .brand { display: flex; align-items: center; gap: .8rem; padding: .3rem .5rem 1.3rem; }
        .brand img { height: 42px; width: auto; display: block; }
        .brand-fallback { display: none; width: 42px; height: 42px; border-radius: var(--radius-md); place-items: center; font-family: "Oswald", sans-serif; font-weight: 700; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep)); }
        .brand-text { font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1.05rem; letter-spacing: .06em; text-transform: uppercase; line-height: 1.1; }
        .brand-text small { display: block; font-family: "Inter", sans-serif; font-size: .6rem; font-weight: 600; letter-spacing: .22em; color: var(--gold-light); margin-top: .22rem; }
        .user-card { display: flex; align-items: center; gap: .8rem; padding: .8rem; margin-bottom: 1.2rem; border: 1px solid rgba(243,217,139,.18); background: linear-gradient(135deg, rgba(243,217,139,.10), rgba(243,217,139,.03)); border-radius: var(--radius-lg); }
        .avatar { width: 44px; height: 44px; border-radius: 50%; flex: none; display: grid; place-items: center; overflow: hidden; font-family: "Oswald", sans-serif; font-weight: 600; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep)); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .user-name { font-weight: 600; font-size: .92rem; color: #fff; }
        .user-role { font-size: .62rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-light); margin-top: .15rem; }
        .nav { flex: 1; }
        .nav-label { font-size: .62rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: rgba(245,240,223,.42); padding: 1rem .8rem .5rem; }
        .nav-item { display: flex; align-items: center; gap: .8rem; padding: .72rem .8rem; margin-bottom: .2rem; border-radius: var(--radius-md); color: rgba(245,240,223,.72); }
        .nav-item:hover { background: rgba(243,217,139,.08); color: #fff; }
        .nav-item.active { background: linear-gradient(135deg, var(--gold-bright), var(--gold)); color: #17130a; font-weight: 600; }
        .side-foot { padding-top: 1rem; border-top: 1px solid rgba(243,217,139,.14); display: grid; gap: .6rem; }
        .btn-site, .btn-signout { display: flex; align-items: center; justify-content: center; gap: .55rem; padding: .75rem 1rem; border-radius: var(--radius-md); }
        .btn-site { font-family: "Oswald", sans-serif; letter-spacing: .12em; text-transform: uppercase; color: var(--gold-bright); border: 1px solid rgba(243,217,139,.4); }
        .btn-signout { font-size: .85rem; font-weight: 600; color: rgba(245,240,223,.7); }
        .main { min-width: 0; display: flex; flex-direction: column; }
        .topbar { position: sticky; top: 0; z-index: 40; display: flex; align-items: center; gap: 1rem; padding: .95rem 2rem; background: var(--header-bg); backdrop-filter: blur(16px); border-bottom: 1px solid var(--line); }
        .menu-btn { display: none; width: 42px; height: 42px; border-radius: var(--radius-md); place-items: center; border: 1px solid var(--line-strong); background: var(--surface); }
        .page-title h1 { font-size: 1.55rem; text-transform: uppercase; }
        .page-title p { font-size: .8rem; color: var(--text-muted); margin-top: .2rem; }
        .top-actions { margin-left: auto; display: flex; align-items: center; gap: .6rem; }
        .icon-btn { width: 42px; height: 42px; border-radius: var(--radius-md); display: grid; place-items: center; background: var(--surface); border: 1px solid var(--line-strong); }
        .theme-btn .sun { display: none; }
        html.dark-mode .theme-btn .sun { display: block; }
        html.dark-mode .theme-btn .moon { display: none; }
        .btn-gold { display: inline-flex; align-items: center; justify-content: center; gap: .55rem; height: 42px; padding: 0 1.15rem; border-radius: var(--radius-md); font-family: "Oswald", sans-serif; font-weight: 600; font-size: .86rem; letter-spacing: .12em; text-transform: uppercase; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold)); }
        .btn-ghost { display: inline-flex; align-items: center; height: 42px; padding: 0 1.1rem; border-radius: var(--radius-md); font-weight: 600; border: 1px solid var(--line-strong); background: var(--surface); }
        .content { padding: 1.75rem 2rem 3rem; display: grid; gap: 1.6rem; max-width: 1100px; width: 100%; margin: 0 auto; }
        .nav { flex: 1; overflow-y: auto; }
        .chip { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .55rem; border-radius: 100px; font-size: .68rem; font-weight: 700; color: var(--gold-text); background: rgba(201,154,46,.14); }
        .thread { list-style: none; display: grid; gap: .7rem; margin-top: .4rem; }
        .thread li { padding: .85rem 1rem; border-radius: 12px; background: var(--bg); border: 1px solid var(--line); }
        .thread li.from-trainer { border-color: rgba(201,154,46,.35); background: rgba(201,154,46,.08); }
        .thread b { display: block; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; color: var(--gold-text); margin-bottom: .35rem; }
        .thread span { font-size: .72rem; color: var(--text-muted); font-weight: 500; }
        .thread p { font-size: .9rem; line-height: 1.5; white-space: pre-wrap; }
        .convo { display: grid; gap: .85rem; padding: 1rem 0; border-top: 1px solid var(--line); }
        .convo:first-of-type { border-top: none; padding-top: 0; }
        .msg-form, .share-form { display: grid; gap: .8rem; }
        .msg-form textarea, .share-form input, .share-form select, .portal-note {
            width: 100%; padding: .8rem 1rem; border-radius: 12px; border: 1px solid var(--line-strong); background: var(--bg); color: var(--text); font: inherit;
        }
        .msg-form textarea { min-height: 90px; resize: vertical; }
        .share-form label span { display: block; font-size: .74rem; color: var(--text-muted); margin-bottom: .35rem; }
        @media (min-width: 720px) { .share-form { grid-template-columns: 1fr 1fr auto; align-items: end; } .share-form .full { grid-column: 1 / -1; } }
        .file-list { list-style: none; display: grid; gap: .65rem; }
        .file-list li { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: .85rem 1rem; border-radius: 12px; background: var(--bg); border: 1px solid var(--line); }
        .file-actions { display: flex; gap: .5rem; flex-wrap: wrap; }
        .btn-logout:hover { border-color: rgba(239,68,68,.5); color: #dc2626; }
        .empty, .prog-note { color: var(--text-muted); }
        .muted { color: var(--text-muted); }
        .td-main { font-weight: 600; }
        .td-sub { font-size: .74rem; color: var(--text-muted); margin-top: .1rem; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; color: var(--text-muted); padding: .8rem 1rem; }
        td { padding: .95rem 1rem; border-top: 1px solid var(--line); vertical-align: middle; font-size: .88rem; }
        .td-name { display: flex; align-items: center; gap: .85rem; }
        .td-avatar { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; font-family: "Oswald", sans-serif; font-weight: 600; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep)); }
        .badge { display: inline-flex; align-items: center; gap: .4rem; padding: .28rem .7rem; border-radius: 100px; font-size: .66rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; border: 1px solid; }
        .st-active { color: #11633c; background: rgba(22,130,80,.10); border-color: rgba(22,130,80,.35); }
        .st-inactive { color: #a11d1d; background: rgba(200,40,40,.08); border-color: rgba(200,40,40,.30); }
        .coach-card { display: grid; grid-template-columns: auto 1fr; gap: 1.2rem 1.4rem; align-items: center; }
        .coach-photo { width: 96px; height: 96px; font-size: 1.85rem; border-radius: 22px; }
        .coach-card h3 { font-size: 1.45rem; text-transform: uppercase; margin-top: .2rem; }
        .coach-card .role { font-size: .68rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-text); }
        .coach-contacts { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: .9rem; }
        .coach-contacts a { display: inline-flex; align-items: center; gap: .4rem; padding: .45rem .8rem; border-radius: 100px; font-size: .82rem; font-weight: 600; color: var(--gold-text); background: rgba(201,154,46,.12); border: 1px solid rgba(201,154,46,.35); }
        .plan { position: relative; overflow: hidden; }
        .plan-top { display: flex; align-items: center; gap: 1.2rem; }
        .plan-ic { width: 64px; height: 64px; border-radius: 16px; display: grid; place-items: center; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep)); }
        .badge-active { margin-left: auto; padding: .4rem .95rem; border-radius: 100px; font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--gold-text); background: rgba(201,154,46,.14); border: 1px solid rgba(201,154,46,.45); }
        .phases { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; margin-top: 1.5rem; }
        .phase { padding-top: .7rem; border-top: 4px solid rgba(23,19,10,.08); font-size: .7rem; color: var(--text-muted); }
        .phase.now { border-color: var(--gold-bright); color: var(--gold-text); }
        .prog-row { display: flex; justify-content: space-between; margin-bottom: .55rem; }
        .bar-wrap { height: 10px; background: rgba(23,19,10,.06); border-radius: 100px; overflow: hidden; margin-bottom: 1.15rem; }
        .bar-fill { height: 100%; border-radius: 100px; background: linear-gradient(90deg, var(--gold-deep), var(--gold) 50%, var(--gold-bright)); }
        .chart { display: grid; grid-template-columns: repeat(7, 1fr); gap: .6rem; align-items: end; height: 150px; }
        .col { display: grid; justify-items: center; align-content: end; gap: .45rem; height: 100%; }
        .col .b { width: 100%; max-width: 34px; border-radius: 8px 8px 4px 4px; min-height: 6px; background: rgba(23,19,10,.06); }
        .col.on .b { background: linear-gradient(180deg, var(--gold-bright), var(--gold) 55%, var(--gold-deep)); }
        .sched { list-style: none; display: grid; gap: .8rem; }
        .sess { display: flex; align-items: center; gap: .85rem; }
        .sess .tile { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; background: rgba(201,154,46,.12); color: var(--gold-text); }
        .sess-info { flex: 1; }
        .sess-info b { display: block; }
        .sess-info span, .day { font-size: .78rem; color: var(--text-muted); }
        .sess-empty { color: var(--text-muted); }
        @media (max-width: 720px) { .coach-card, .phases { grid-template-columns: 1fr; } }
        .card { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; padding: 1.4rem 1.5rem; }
        .card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.1rem; }
        .card-head h3 { display: flex; align-items: center; gap: .5rem; font-size: 1.15rem; }
        .flash { padding: 1rem 1.2rem; border-radius: 12px; font-weight: 600; }
        .flash-ok { color: #11633c; background: rgba(22,130,80,.10); border: 1px solid rgba(22,130,80,.35); }
        html.dark-mode .flash-ok { color: #6ee7b7; background: rgba(52,211,153,.10); border-color: rgba(52,211,153,.35); }
        .flash-err { color: #a11d1d; background: rgba(200,40,40,.08); border: 1px solid rgba(200,40,40,.30); }
        .profile-form { display: grid; gap: 1rem; }
        .profile-grid { display: grid; gap: 1rem; }
        @media (min-width: 720px) { .profile-grid { grid-template-columns: 1fr 1fr; } .profile-grid .full { grid-column: 1 / -1; } }
        .profile-form label span { display: block; font-size: .74rem; color: var(--text-muted); margin-bottom: .35rem; }
        .profile-form input, .profile-form select, .profile-form textarea { width: 100%; padding: .8rem 1rem; border-radius: 12px; border: 1px solid var(--line-strong); background: var(--bg); color: var(--text); font: inherit; }
        .profile-form textarea { min-height: 90px; resize: vertical; }
        .profile-photo { display: flex; align-items: center; gap: 1rem; }
        .profile-photo .avatar { width: 72px; height: 72px; font-size: 1.3rem; }
        .overlay { display: none; }
        @media (max-width: 920px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: fixed; inset: 0 auto 0 0; width: 276px; transform: translateX(-110%); transition: transform .25s; }
            body.nav-open .sidebar { transform: none; }
            .overlay { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 40; opacity: 0; pointer-events: none; }
            body.nav-open .overlay { opacity: 1; pointer-events: auto; }
            .menu-btn { display: grid; }
            .hide-sm { display: none !important; }
        }
    </style>
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-dash" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></symbol>
    <symbol id="i-msg" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-clipboard" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="18" rx="2.5"/><path d="M9 2h6v4H9zM9 12h6M9 16h4"/></symbol>
    <symbol id="i-run" viewBox="0 0 24 24"><circle cx="14" cy="4.5" r="1.8"/><path d="M5 12l3-3 4 1 2 3 3 1M9 21l2-6-2.5-2.5M14 14l1.5 3.5L19 19"/></symbol>
    <symbol id="i-dumbbell" viewBox="0 0 24 24"><path d="M6.5 6.5v11M17.5 6.5v11M3 9v6M21 9v6M6.5 12h11"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="i-trend" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol>
    <symbol id="i-leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.5 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.5 6-7"/></symbol>
    <symbol id="i-card" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20M6 15h4"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.5 3h3l1.5 4-2 1.5a12 12 0 0 0 6 6L17 13l4 1.5v3A16 16 0 0 1 6.5 3z"/></symbol>
    <symbol id="i-activity" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
    <symbol id="i-yoga" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.8"/><path d="M12 8v5M7.5 10.5L12 8l4.5 2.5M9.5 21l2.5-8 2.5 8"/></symbol>
    <symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></symbol>
    <symbol id="i-up-right" viewBox="0 0 24 24"><path d="M7 17L17 7M8 7h9v9"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></symbol>
</svg>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <a href="{{ $websiteUrl }}" class="brand">
            <img src="{{ $websiteUrl }}logo/logo.png" alt="TFI" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" />
            <span class="brand-fallback">TFI</span>
            <span class="brand-text">TFI Portal<small>{{ $isTrainee ? 'My Fitness Portal' : 'Trainer Panel' }}</small></span>
        </a>
        <div class="user-card">
            <div class="avatar">
                @if ($me->avatarUrl())
                    <img src="{{ $me->avatarUrl() }}" alt="{{ $me->name }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div>
                <div class="user-name">{{ $me->name }}</div>
                <div class="user-role">{{ $roleLabel }}</div>
            </div>
        </div>
        <nav class="nav">
            @include('dashboard.partials.portal-nav')
        </nav>
        <div class="side-foot">
            <a href="{{ $websiteUrl }}" class="btn-site"><svg class="ic"><use href="#i-globe"/></svg> Visit Website</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-signout"><svg class="ic"><use href="#i-logout"/></svg> Sign Out</button>
            </form>
        </div>
    </aside>
    <div class="overlay" id="overlay"></div>
    <div class="main">
        <header class="topbar">
            <button class="menu-btn" id="menuBtn" aria-label="Open menu"><svg class="ic"><use href="#i-menu"/></svg></button>
            <div class="page-title">
                <h1>@yield('heading', 'Profile settings')</h1>
                <p>@yield('subheading', \App\Support\DashboardGreeting::today())</p>
            </div>
            <div class="top-actions">
                <a href="{{ route($dashboardRoute) }}" class="btn-ghost hide-sm">Back to dashboard</a>
                <button class="icon-btn theme-btn" id="themeBtn" aria-label="Toggle theme">
                    <svg class="ic moon"><use href="#i-moon"/></svg>
                    <svg class="ic sun"><use href="#i-sun"/></svg>
                </button>
            </div>
        </header>
        <main class="content">
            @if (session('success'))
                <div class="flash flash-ok">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-err">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script>
    document.getElementById('themeBtn').addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark-mode');
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) {}
    });
    const close = () => document.body.classList.remove('nav-open');
    document.getElementById('menuBtn').addEventListener('click', () => document.body.classList.add('nav-open'));
    document.getElementById('overlay').addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
</script>
</body>
</html>
