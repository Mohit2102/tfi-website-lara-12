@php
    // Change this if your public website lives at a different address
    $websiteUrl = 'https://tfi-3fc3.onrender.com/';

    $me = auth()->user();
    $initialsOf = fn ($name) => collect(preg_split('/\s+/', trim($name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
    $greet = \App\Support\DashboardGreeting::line($me->name);

    $totalC    = max(1, (int) ($stats['total_customers'] ?? 0));
    $activeC   = (int) ($stats['active_customers'] ?? 0);
    $inactiveC = (int) ($stats['inactive_customers'] ?? 0);
    $activePct = min(100, (int) round($activeC / $totalC * 100));
    $ringOff   = round(326.7 * (1 - $activePct / 100), 1);
    $pct       = fn ($n) => round($n / $totalC * 100, 1);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>Admin Dashboard — TFI Portal</title>
    <meta name="description" content="TFI Admin - Manage customers and training programs" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet" />
    <script>
        try { if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark-mode'); } catch (e) {}
    </script>
    <style>
        :root {
            --bg: #f7f4ed;
            --surface: #ffffff;
            --surface-2: #eee9df;

            --gold: #c99a2e;
            --gold-light: #e2bd5b;
            --gold-bright: #f3d98b;
            --gold-deep: #8f6819;
            --gold-dark: #76500d;

            --text: #17130a;
            --text-muted: #5b5240;
            --line: rgba(23, 19, 10, 0.10);
            --line-strong: rgba(23, 19, 10, 0.18);
            --primary-glow: rgba(201, 154, 46, 0.30);
            --soft: rgba(23, 19, 10, 0.05);
            --header-bg: rgba(247, 244, 237, 0.86);

            --ok-bg: rgba(22, 130, 80, .10);  --ok-line: rgba(22, 130, 80, .35);  --ok-text: #11633c;
            --err-bg: rgba(200, 40, 40, .08); --err-line: rgba(200, 40, 40, .30); --err-text: #a11d1d;

            --radius-md: 10px;
            --radius-lg: 18px;
            --shadow-sm: 0 8px 24px rgba(23, 19, 10, 0.06);
            --shadow-md: 0 18px 40px rgba(23, 19, 10, 0.11);
            --shadow-lg: 0 24px 60px rgba(23, 19, 10, 0.18);

            --gold-text: #8f6819;
            color-scheme: light;
        }
        html.dark-mode {
            --bg: #090806;
            --surface: #12100d;
            --surface-2: #1a1711;

            --gold: #d4a83a;
            --gold-light: #e8c765;
            --gold-bright: #f7df9a;
            --gold-deep: #a8791c;
            --gold-dark: #8b6313;

            --text: #f5f0df;
            --text-muted: #b9b2a0;
            --line: rgba(245, 240, 223, 0.10);
            --line-strong: rgba(245, 240, 223, 0.20);
            --primary-glow: rgba(212, 168, 58, 0.34);
            --soft: rgba(245, 240, 223, 0.06);
            --header-bg: rgba(9, 8, 6, 0.86);

            --ok-bg: rgba(52, 211, 153, .10);  --ok-line: rgba(52, 211, 153, .35);  --ok-text: #6ee7b7;
            --err-bg: rgba(239, 68, 68, .10);  --err-line: rgba(239, 68, 68, .35);  --err-text: #fca5a5;

            --gold-text: #e8c765;
            color-scheme: dark;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--bg); color: var(--text);
            font-family: "Inter", system-ui, sans-serif; font-size: 15px;
            -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility;
            transition: background-color .35s ease, color .35s ease;
        }
        h1, h2, h3, .disp { font-family: "Oswald", sans-serif; font-weight: 600; line-height: 1.1; letter-spacing: .01em; }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; color: inherit; cursor: pointer; border: none; background: none; }

        .ic { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; flex: none; }

        /* ───────── App shell ───────── */
        .app { display: grid; grid-template-columns: 276px minmax(0, 1fr); min-height: 100vh; }

        /* ───────── Sidebar (always dark + gold) ───────── */
        .sidebar {
            position: sticky; top: 0; height: 100vh;
            display: flex; flex-direction: column;
            padding: 1.4rem 1.1rem;
            background:
                radial-gradient(120% 40% at 0% 0%, rgba(212,168,58,.20), transparent 60%),
                linear-gradient(180deg, #100d07 0%, #090806 100%);
            color: #f5f0df;
            border-right: 1px solid rgba(243,217,139,.14);
            z-index: 50;
        }
        .brand { display: flex; align-items: center; gap: .8rem; padding: .3rem .5rem 1.3rem; }
        .brand img { height: 42px; width: auto; display: block; }
        .brand-fallback {
            display: none; width: 42px; height: 42px; border-radius: var(--radius-md); place-items: center;
            font-family: "Oswald", sans-serif; font-weight: 700; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
        }
        .brand-text { font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1.05rem; letter-spacing: .06em; text-transform: uppercase; line-height: 1.1; }
        .brand-text small { display: block; font-family: "Inter", sans-serif; font-size: .6rem; font-weight: 600; letter-spacing: .22em; color: var(--gold-light); margin-top: .22rem; }

        .user-card {
            display: flex; align-items: center; gap: .8rem;
            padding: .8rem; margin-bottom: 1.2rem;
            border: 1px solid rgba(243,217,139,.18);
            background: linear-gradient(135deg, rgba(243,217,139,.10), rgba(243,217,139,.03));
            border-radius: var(--radius-lg);
        }
        .avatar {
            width: 44px; height: 44px; border-radius: 50%; flex: none;
            display: grid; place-items: center;
            font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1rem; letter-spacing: .04em; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
            box-shadow: 0 0 0 3px rgba(243,217,139,.18);
        }
        .user-meta { min-width: 0; flex: 1; }
        .user-name { font-weight: 600; font-size: .92rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: .62rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-light); margin-top: .15rem; }
        .online { width: 9px; height: 9px; border-radius: 50%; background: #34d399; box-shadow: 0 0 0 3px rgba(52,211,153,.2); flex: none; }

        .nav { flex: 1; overflow-y: auto; margin: 0 -.3rem; padding: 0 .3rem; scrollbar-width: thin; scrollbar-color: rgba(243,217,139,.25) transparent; }
        .nav-label { font-size: .62rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: rgba(245,240,223,.42); padding: 1rem .8rem .5rem; }
        .nav-item {
            position: relative; display: flex; align-items: center; gap: .8rem;
            padding: .72rem .8rem; margin-bottom: .2rem;
            border-radius: var(--radius-md);
            font-size: .9rem; font-weight: 500; color: rgba(245,240,223,.72);
            transition: background .2s, color .2s, transform .2s;
        }
        .nav-item .ic { width: 19px; height: 19px; color: rgba(245,240,223,.55); transition: color .2s; }
        .nav-item:hover { background: rgba(243,217,139,.08); color: #fff; transform: translateX(3px); }
        .nav-item:hover .ic { color: var(--gold-light); }
        .nav-item.active {
            color: #17130a; font-weight: 600;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold-light) 40%, var(--gold));
            box-shadow: 0 10px 24px rgba(212,168,58,.28);
        }
        .nav-item.active .ic { color: #17130a; }
        .nav-item.active:hover { transform: none; color: #17130a; }

        .side-foot { padding-top: 1rem; margin-top: .6rem; border-top: 1px solid rgba(243,217,139,.14); display: grid; gap: .6rem; }
        .btn-site {
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            padding: .8rem 1rem; border-radius: var(--radius-md);
            font-family: "Oswald", sans-serif; font-weight: 500; font-size: .9rem; letter-spacing: .12em; text-transform: uppercase;
            color: var(--gold-bright);
            border: 1px solid rgba(243,217,139,.4);
            background: rgba(243,217,139,.07);
            transition: all .25s;
        }
        .btn-site:hover { background: linear-gradient(135deg, var(--gold-bright), var(--gold)); color: #17130a; border-color: transparent; transform: translateY(-2px); box-shadow: 0 12px 26px rgba(212,168,58,.3); }
        .btn-site .ic { width: 17px; height: 17px; }
        .btn-signout {
            display: flex; align-items: center; justify-content: center; gap: .55rem; width: 100%;
            padding: .7rem 1rem; border-radius: var(--radius-md);
            font-size: .85rem; font-weight: 600; color: rgba(245,240,223,.7);
            transition: all .2s;
        }
        .btn-signout:hover { color: #fca5a5; background: rgba(239,68,68,.10); }
        .btn-signout .ic { width: 17px; height: 17px; }

        /* ───────── Main column ───────── */
        .main { min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            position: sticky; top: 0; z-index: 40;
            display: flex; align-items: center; gap: 1rem;
            padding: .95rem 2rem;
            background: var(--header-bg);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--line);
        }
        .menu-btn { display: none; width: 42px; height: 42px; border-radius: var(--radius-md); place-items: center; border: 1px solid var(--line-strong); background: var(--surface); }
        .page-title h1 { font-size: 1.55rem; text-transform: uppercase; color: var(--text); }
        .page-title p { font-size: .8rem; color: var(--text-muted); margin-top: .2rem; }
        .top-actions { margin-left: auto; display: flex; align-items: center; gap: .6rem; }
        .icon-btn {
            position: relative; width: 42px; height: 42px; border-radius: var(--radius-md);
            display: grid; place-items: center; color: var(--text);
            background: var(--surface); border: 1px solid var(--line-strong);
            transition: all .2s;
        }
        .icon-btn:hover { border-color: var(--gold); color: var(--gold-text); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .theme-btn .sun { display: none; }
        html.dark-mode .theme-btn .sun { display: block; }
        html.dark-mode .theme-btn .moon { display: none; }

        .btn-gold {
            display: inline-flex; align-items: center; justify-content: center; gap: .55rem;
            height: 42px; padding: 0 1.15rem; border-radius: var(--radius-md);
            font-family: "Oswald", sans-serif; font-weight: 600; font-size: .86rem; letter-spacing: .12em; text-transform: uppercase;
            color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold-light) 35%, var(--gold) 70%, var(--gold-deep));
            box-shadow: 0 10px 22px var(--primary-glow), inset 0 1px 0 rgba(255,255,255,.45);
            transition: transform .25s, box-shadow .25s;
            position: relative; overflow: hidden;
        }
        .btn-gold::after { content: ''; position: absolute; top: 0; left: -120%; width: 55%; height: 100%; background: linear-gradient(100deg, transparent, rgba(255,255,255,.5), transparent); transform: skewX(-20deg); transition: left .6s; }
        .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 16px 32px var(--primary-glow), inset 0 1px 0 rgba(255,255,255,.5); }
        .btn-gold:hover::after { left: 140%; }
        .btn-gold .ic { width: 17px; height: 17px; }

        .btn-ghost {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            height: 42px; padding: 0 1.1rem; border-radius: var(--radius-md);
            font-size: .85rem; font-weight: 600;
            color: var(--text); background: var(--surface); border: 1px solid var(--line-strong);
            transition: all .2s;
        }
        .btn-ghost:hover { border-color: var(--gold); color: var(--gold-text); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .btn-ghost .ic { width: 17px; height: 17px; }
        .btn-logout:hover { border-color: rgba(239,68,68,.5); color: #dc2626; }

        .content { padding: 1.75rem 2rem 3rem; display: grid; gap: 1.6rem; max-width: 1280px; width: 100%; margin: 0 auto; }

        /* ───────── Hero ───────── */
        .hero {
            position: relative; overflow: hidden; isolation: isolate;
            display: grid; grid-template-columns: 1fr auto; gap: 2rem; align-items: center;
            padding: 2.2rem 2.4rem;
            border-radius: 24px; color: #f5f0df;
            background:
                linear-gradient(100deg, rgba(9,8,6,.96) 0%, rgba(9,8,6,.84) 55%, rgba(9,8,6,.6) 100%),
                url('https://tfi-3fc3.onrender.com/images/hero-2.png') center / cover no-repeat,
                #090806;
            border: 1px solid rgba(243,217,139,.25);
            box-shadow: var(--shadow-lg);
        }
        .hero::before { content: ''; position: absolute; z-index: -1; width: 480px; height: 480px; border-radius: 50%; right: -140px; top: -200px; background: radial-gradient(circle, rgba(212,168,58,.45), transparent 65%); }
        .hero::after { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright), var(--gold-deep)); }
        .eyebrow { display: inline-flex; align-items: center; gap: .55rem; font-size: .68rem; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: var(--gold-light); margin-bottom: .9rem; }
        .eyebrow::before { content: ''; width: 28px; height: 2px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright)); }
        .hero h2 { font-size: clamp(1.7rem, 3.2vw, 2.6rem); text-transform: uppercase; color: #fff; }
        .hero h2 .hl { background: linear-gradient(95deg, var(--gold-bright), var(--gold) 60%, var(--gold-light)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: rgba(245,240,223,.78); margin-top: .8rem; max-width: 540px; line-height: 1.65; }
        .hero-cta { display: flex; flex-wrap: wrap; gap: .7rem; margin-top: 1.5rem; }
        .hero .btn-ghost { background: rgba(255,255,255,.08); color: #fff; border-color: rgba(255,255,255,.25); }
        .hero .btn-ghost:hover { background: rgba(243,217,139,.16); color: var(--gold-bright); border-color: var(--gold-light); }

        .ring-card {
            display: grid; justify-items: center; gap: .6rem; padding: 1.2rem 1.5rem;
            border-radius: var(--radius-lg);
            background: rgba(255,255,255,.07); border: 1px solid rgba(243,217,139,.25);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        }
        .ring { position: relative; width: 132px; height: 132px; }
        .ring svg { width: 100%; height: 100%; transform: rotate(-90deg); }
        .ring .track { fill: none; stroke: rgba(255,255,255,.14); stroke-width: 9; }
        .ring .bar { fill: none; stroke: url(#goldGrad); stroke-width: 9; stroke-linecap: round; stroke-dasharray: 326.7; stroke-dashoffset: 326.7; animation: ring 1.6s .3s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes ring { to { stroke-dashoffset: var(--off); } }
        .ring-val { position: absolute; inset: 0; display: grid; place-items: center; text-align: center; }
        .ring-val b { font-family: "Oswald", sans-serif; font-size: 2rem; font-weight: 600; color: #fff; line-height: 1; }
        .ring-val b i { font-style: normal; font-size: 1rem; color: var(--gold-light); }
        .ring-cap { font-size: .66rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: rgba(245,240,223,.7); text-align: center; }

        /* ───────── Stats ───────── */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .stat {
            position: relative; overflow: hidden;
            padding: 1.25rem 1.3rem 1.2rem;
            background: var(--surface); border: 1px solid var(--line-strong); border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm); transition: transform .25s, box-shadow .25s, border-color .25s;
        }
        .stat::before { content: ''; position: absolute; left: 0; right: 0; top: 0; height: 3px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright), var(--gold-deep)); }
        .stat:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: var(--gold); }
        .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .tile-ic {
            width: 46px; height: 46px; border-radius: 13px; display: grid; place-items: center; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
            box-shadow: 0 8px 18px var(--primary-glow);
        }
        .chip { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .6rem; border-radius: 100px; font-size: .68rem; font-weight: 700; letter-spacing: .04em; color: var(--gold-text); background: rgba(201,154,46,.14); border: 1px solid rgba(201,154,46,.35); white-space: nowrap; }
        .chip .ic { width: 12px; height: 12px; stroke-width: 2.6; }
        .chip.warn { color: var(--err-text); background: var(--err-bg); border-color: var(--err-line); }
        .stat-value { font-family: "Oswald", sans-serif; font-size: 2.6rem; font-weight: 600; line-height: 1; color: var(--text); }
        .stat-label { margin-top: .55rem; font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-muted); }

        /* ───────── Cards ───────── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1.3fr; gap: 1.4rem; align-items: start; }
        .card { background: var(--surface); border: 1px solid var(--line-strong); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 1.5rem; }
        .card.flush { padding: 0; overflow: hidden; }
        .card-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.2rem; }
        .card.flush .card-head { padding: 1.3rem 1.5rem 0; margin-bottom: 1rem; }
        .card-head h3 { font-size: 1.1rem; text-transform: uppercase; display: flex; align-items: center; gap: .6rem; }
        .card-head h3 .ic { color: var(--gold-text); }
        .link { display: inline-flex; align-items: center; gap: .35rem; font-size: .82rem; font-weight: 600; color: var(--gold-text); transition: gap .2s; }
        .link:hover { gap: .6rem; text-decoration: underline; text-underline-offset: 3px; }
        .link .ic { width: 15px; height: 15px; }

        /* distribution */
        .dist-bar { display: flex; height: 14px; border-radius: 100px; overflow: hidden; background: var(--soft); gap: 3px; }
        .dist-bar span { display: block; height: 100%; min-width: 0; }
        .dist-bar .s1 { background: linear-gradient(90deg, var(--gold-deep), var(--gold)); }
        .dist-bar .s2 { background: var(--line-strong); }
        .legend { display: grid; gap: .8rem; margin-top: 1.3rem; }
        .legend-row { display: flex; align-items: center; gap: .7rem; font-size: .88rem; }
        .legend-row i { width: 11px; height: 11px; border-radius: 4px; flex: none; }
        .legend-row .s1 { background: var(--gold-deep); }
        .legend-row .s2 { background: var(--line-strong); }
        .legend-row b { margin-left: auto; font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1.05rem; }
        .legend-row small { color: var(--text-muted); font-size: .74rem; width: 3.2rem; text-align: right; }

        /* quick actions */
        .actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: .8rem; }
        .qa {
            display: flex; align-items: center; gap: .85rem; padding: 1rem 1.05rem;
            background: var(--bg); border: 1px solid var(--line-strong); border-radius: 14px;
            transition: all .25s;
        }
        .qa:hover { transform: translateY(-4px); border-color: var(--gold); box-shadow: var(--shadow-md); background: var(--surface); }
        .qa .tile { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; flex: none; color: var(--gold-text); background: rgba(201,154,46,.14); transition: all .25s; }
        .qa:hover .tile { color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold)); box-shadow: 0 8px 16px var(--primary-glow); }
        .qa b { font-size: .9rem; font-weight: 600; }

        /* table */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 620px; }
        thead th {
            text-align: left; padding: .8rem 1.5rem;
            font-size: .66rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--text-muted);
            background: var(--soft); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }
        tbody td { padding: .95rem 1.5rem; border-bottom: 1px solid var(--line); vertical-align: middle; font-size: .88rem; }
        tbody tr { transition: background .2s; }
        tbody tr:hover { background: rgba(201,154,46,.07); }
        tbody tr:last-child td { border-bottom: none; }
        .td-name { display: flex; align-items: center; gap: .85rem; }
        .td-avatar {
            width: 40px; height: 40px; border-radius: 12px; flex: none; display: grid; place-items: center;
            font-family: "Oswald", sans-serif; font-weight: 600; font-size: .9rem; letter-spacing: .04em; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
        }
        .td-main { font-weight: 600; }
        .muted { color: var(--text-muted); }

        .badge { display: inline-flex; align-items: center; gap: .4rem; padding: .28rem .7rem; border-radius: 100px; font-size: .66rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; white-space: nowrap; border: 1px solid; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .st-active { color: var(--ok-text); background: var(--ok-bg); border-color: var(--ok-line); }
        .st-inactive { color: var(--err-text); background: var(--err-bg); border-color: var(--err-line); }

        .empty { padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); }

        /* website CTA */
        .site-cta {
            position: relative; overflow: hidden; isolation: isolate;
            display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;
            padding: 1.7rem 2rem; border-radius: 22px; color: #f5f0df;
            background: linear-gradient(120deg, #17130a 0%, #2c2109 55%, #4a350d 100%);
            border: 1px solid rgba(243,217,139,.28); box-shadow: var(--shadow-md);
        }
        .site-cta::before { content: ''; position: absolute; z-index: -1; width: 380px; height: 380px; border-radius: 50%; right: -100px; top: -160px; background: radial-gradient(circle, rgba(243,217,139,.35), transparent 65%); }
        .site-cta h3 { font-size: 1.5rem; text-transform: uppercase; color: #fff; }
        .site-cta h3 span { color: var(--gold-light); }
        .site-cta p { margin-top: .4rem; font-size: .9rem; color: rgba(245,240,223,.75); }

        .overlay { display: none; }

        /* ───────── Responsive ───────── */
        @media (max-width: 1180px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 980px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: fixed; inset: 0 auto 0 0; width: 284px; transform: translateX(-100%); transition: transform .35s cubic-bezier(.16,1,.3,1); box-shadow: 20px 0 60px rgba(0,0,0,.5); }
            body.nav-open .sidebar { transform: none; }
            .overlay { display: block; position: fixed; inset: 0; z-index: 45; background: rgba(0,0,0,.55); opacity: 0; pointer-events: none; transition: opacity .3s; }
            body.nav-open .overlay { opacity: 1; pointer-events: auto; }
            .menu-btn { display: grid; }
            .topbar { padding: .85rem 1.1rem; }
            .content { padding: 1.25rem 1.1rem 2.5rem; }
            .btn-ghost.hide-sm span, .btn-gold.hide-sm span { display: none; }
        }
        @media (max-width: 720px) {
            .hero { grid-template-columns: 1fr; padding: 1.6rem 1.4rem; }
            .ring-card { justify-self: start; grid-auto-flow: column; align-items: center; gap: 1rem; }
            .ring { width: 96px; height: 96px; }
            .ring-val b { font-size: 1.5rem; }
            .page-title h1 { font-size: 1.2rem; }
            .page-title p { display: none; }
            thead th, tbody td { padding-left: 1rem; padding-right: 1rem; }
            .card.flush .card-head { padding: 1.1rem 1rem 0; }
        }
        @media (max-width: 520px) {
            .stats { grid-template-columns: 1fr 1fr; gap: .7rem; }
            .stat { padding: 1rem; }
            .stat-value { font-size: 2rem; }
            .actions { grid-template-columns: 1fr; }
            .site-cta { padding: 1.4rem; }
            .btn-gold.full-sm { width: 100%; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition: none !important; }
            .ring .bar { stroke-dashoffset: var(--off); }
        }
    </style>
</head>
<body>

<!-- ───── Icon sprite ───── -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="goldGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#f7df9a"/><stop offset=".55" stop-color="#d4a83a"/><stop offset="1" stop-color="#a8791c"/>
        </linearGradient>
    </defs>
    <symbol id="i-dash" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></symbol>
    <symbol id="i-run" viewBox="0 0 24 24"><circle cx="14" cy="4.5" r="1.8"/><path d="M5 12l3-3 4 1 2 3 3 1M9 21l2-6-2.5-2.5M14 14l1.5 3.5L19 19"/></symbol>
    <symbol id="i-user-plus" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6M19 8v6M16 11h6"/></symbol>
    <symbol id="i-check-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-6"/></symbol>
    <symbol id="i-x-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></symbol>
    <symbol id="i-dumbbell" viewBox="0 0 24 24"><path d="M6.5 6.5v11M17.5 6.5v11M3 9v6M21 9v6M6.5 12h11"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="i-clipboard" viewBox="0 0 24 24"><rect x="5" y="4" width="14" height="18" rx="2.5"/><path d="M9 2h6v4H9zM9 12h6M9 16h4"/></symbol>
    <symbol id="i-msg" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></symbol>
    <symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></symbol>
    <symbol id="i-up-right" viewBox="0 0 24 24"><path d="M7 17L17 7M8 7h9v9"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="i-pie" viewBox="0 0 24 24"><path d="M21 12A9 9 0 1 1 12 3v9z"/><path d="M15 3.5A9 9 0 0 1 20.5 9H15z"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
    <symbol id="i-down" viewBox="0 0 24 24"><path d="M12 5v14M19 12l-7 7-7-7"/></symbol>
</svg>

<div class="app">

    <!-- ═════════ Sidebar ═════════ -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ $websiteUrl }}" class="brand" aria-label="TFI — Time to Fit India">
            <img src="{{ $websiteUrl }}logo/logo.png" alt="TFI"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" />
            <span class="brand-fallback">TFI</span>
            <span class="brand-text">TFI Portal<small>Admin Panel</small></span>
        </a>

        <div class="user-card">
            <div class="avatar">{{ $initialsOf($me->name ?? '') }}</div>
            <div class="user-meta">
                <div class="user-name">{{ $me->name }}</div>
                <div class="user-role">Admin</div>
            </div>
            <span class="online" title="Online"></span>
        </div>

        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item active"><svg class="ic"><use href="#i-dash"/></svg> Dashboard</a>

            <p class="nav-label">Customers</p>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-run"/></svg> All Customers</a>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-user-plus"/></svg> Add Customer</a>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-check-circle"/></svg> Active</a>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-x-circle"/></svg> Inactive</a>

            <p class="nav-label">Programs</p>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-dumbbell"/></svg> Training Plans</a>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-calendar"/></svg> Schedules</a>
            <a href="#" class="nav-item"><svg class="ic"><use href="#i-clipboard"/></svg> Reports</a>
        </nav>

        <div class="side-foot">
            <a href="{{ $websiteUrl }}" class="btn-site">
                <svg class="ic"><use href="#i-globe"/></svg> Visit Website
            </a>
            @if (Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-signout"><svg class="ic"><use href="#i-logout"/></svg> Sign Out</button>
                </form>
            @endif
        </div>
    </aside>
    <div class="overlay" id="overlay"></div>

    <!-- ═════════ Main ═════════ -->
    <div class="main">

        <header class="topbar">
            <button class="menu-btn" id="menuBtn" aria-label="Open menu"><svg class="ic"><use href="#i-menu"/></svg></button>
            <div class="page-title">
                <h1>Admin Dashboard</h1>
                <p>{{ \App\Support\DashboardGreeting::today() }}</p>
            </div>

            <div class="top-actions">
                <a href="{{ $websiteUrl }}" class="btn-gold hide-sm" title="Go to website">
                    <svg class="ic"><use href="#i-globe"/></svg><span>Visit Website</span><svg class="ic"><use href="#i-up-right"/></svg>
                </a>
                <button class="icon-btn theme-btn" id="themeBtn" aria-label="Toggle theme" title="Toggle light / dark">
                    <svg class="ic moon"><use href="#i-moon"/></svg>
                    <svg class="ic sun"><use href="#i-sun"/></svg>
                </button>
                <a href="#" class="icon-btn" aria-label="Notifications"><svg class="ic"><use href="#i-bell"/></svg></a>
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost btn-logout hide-sm"><svg class="ic"><use href="#i-logout"/></svg><span>Logout</span></button>
                    </form>
                @endif
            </div>
        </header>

        <main class="content">

            <!-- Hero -->
            <section class="hero">
                <div>
                    <div class="eyebrow">{{ $greet }}</div>
                    <h2>Welcome back, <span class="hl">{{ $me->name }}</span>!</h2>
                    <p>Manage your customers and training programs from here.</p>
                    <div class="hero-cta">
                        <a href="#" class="btn-gold"><svg class="ic"><use href="#i-user-plus"/></svg> Add customer</a>
                        <a href="{{ $websiteUrl }}" class="btn-ghost"><svg class="ic"><use href="#i-globe"/></svg> Visit website</a>
                    </div>
                </div>

                <div class="ring-card">
                    <div class="ring" style="--off: {{ $ringOff }};">
                        <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="bar" cx="60" cy="60" r="52"/></svg>
                        <div class="ring-val"><b>{{ $activePct }}<i>%</i></b></div>
                    </div>
                    <div class="ring-cap">Customers active<br>{{ $activeC }} of {{ $stats['total_customers'] }}</div>
                </div>
            </section>

            <!-- Stats -->
            <section class="stats">
                <article class="stat">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-run"/></svg></span></div>
                    <div class="stat-value">{{ $stats['total_customers'] }}</div>
                    <div class="stat-label">Total customers</div>
                </article>
                <article class="stat">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-check-circle"/></svg></span><span class="chip"><svg class="ic"><use href="#i-up"/></svg>Online</span></div>
                    <div class="stat-value">{{ $stats['active_customers'] }}</div>
                    <div class="stat-label">Active customers</div>
                </article>
                <article class="stat">
                    <div class="stat-top">
                        <span class="tile-ic"><svg class="ic"><use href="#i-x-circle"/></svg></span>
                        @if ($stats['inactive_customers'] > 0)
                            <span class="chip warn"><svg class="ic"><use href="#i-down"/></svg>Needs attention</span>
                        @endif
                    </div>
                    <div class="stat-value">{{ $stats['inactive_customers'] }}</div>
                    <div class="stat-label">Inactive customers</div>
                </article>
                <article class="stat">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-dumbbell"/></svg></span>@if (!empty($stats['active_programs']))<span class="chip"><svg class="ic"><use href="#i-up"/></svg>Running</span>@endif</div>
                    <div class="stat-value">{{ $stats['active_programs'] ?? '—' }}</div>
                    <div class="stat-label">Active programs</div>
                </article>
            </section>

            <!-- Status mix + Quick actions -->
            <section class="grid-2">
                <div class="card">
                    <div class="card-head">
                        <h3><svg class="ic"><use href="#i-pie"/></svg> Customer status</h3>
                    </div>
                    <div class="dist-bar" role="img" aria-label="Active versus inactive customers">
                        <span class="s1" style="flex: {{ $activeC }} 1 0"></span>
                        @if ($inactiveC > 0)<span class="s2" style="flex: {{ $inactiveC }} 1 0"></span>@endif
                    </div>
                    <div class="legend">
                        <div class="legend-row"><i class="s1"></i> Active <b>{{ $activeC }}</b><small>{{ $pct($activeC) }}%</small></div>
                        <div class="legend-row"><i class="s2"></i> Inactive <b>{{ $inactiveC }}</b><small>{{ $pct($inactiveC) }}%</small></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h3><svg class="ic"><use href="#i-zap"/></svg> Quick actions</h3>
                    </div>
                    <div class="actions">
                        <a href="#" class="qa"><span class="tile"><svg class="ic"><use href="#i-user-plus"/></svg></span><b>Add Customer</b></a>
                        <a href="#" class="qa"><span class="tile"><svg class="ic"><use href="#i-calendar"/></svg></span><b>Schedule</b></a>
                        <a href="#" class="qa"><span class="tile"><svg class="ic"><use href="#i-clipboard"/></svg></span><b>Reports</b></a>
                        <a href="#" class="qa"><span class="tile"><svg class="ic"><use href="#i-msg"/></svg></span><b>Messages</b></a>
                    </div>
                </div>
            </section>

            <!-- Recent customers -->
            <section class="card flush">
                <div class="card-head">
                    <h3><svg class="ic"><use href="#i-clock"/></svg> Recent customers</h3>
                    <a href="#" class="link">View all <svg class="ic"><use href="#i-arrow"/></svg></a>
                </div>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentCustomers as $customer)
                            <tr>
                                <td>
                                    <div class="td-name">
                                        <div class="td-avatar">{{ $initialsOf($customer->name) }}</div>
                                        <div class="td-main">{{ $customer->name }}</div>
                                    </div>
                                </td>
                                <td class="muted">{{ $customer->email }}</td>
                                <td class="muted">{{ $customer->phone ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $customer->is_active ? 'st-active' : 'st-inactive' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="muted" style="font-size:.82rem; white-space:nowrap">{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty">No customers yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Website CTA -->
            <section class="site-cta">
                <div>
                    <h3>See what your members see on the <span>TFI website</span></h3>
                    <p>Check programs, pricing and trainer pages exactly as customers do.</p>
                </div>
                <a href="{{ $websiteUrl }}" class="btn-gold full-sm">
                    <svg class="ic"><use href="#i-globe"/></svg> Go to website <svg class="ic"><use href="#i-up-right"/></svg>
                </a>
            </section>

        </main>
    </div>
</div>

<script>
    // Theme toggle (shares the 'theme' key with the main site)
    document.getElementById('themeBtn').addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark-mode');
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (e) {}
    });

    // Mobile sidebar
    const close = () => document.body.classList.remove('nav-open');
    document.getElementById('menuBtn').addEventListener('click', () => document.body.classList.add('nav-open'));
    document.getElementById('overlay').addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
</script>
</body>
</html>
