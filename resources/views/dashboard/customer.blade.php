@php
    $websiteUrl = rtrim(url('/'), '/').'/';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>My Dashboard — TFI Portal</title>
    <meta name="description" content="TFI Customer Dashboard - Track your fitness journey" />
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
            display: grid; place-items: center; overflow: hidden;
            font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1rem; letter-spacing: .04em; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
            box-shadow: 0 0 0 3px rgba(243,217,139,.18);
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
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
        .icon-btn .dot { position: absolute; top: 9px; right: 10px; width: 8px; height: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 0 2px var(--surface); }
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
                linear-gradient(100deg, rgba(9,8,6,.96) 0%, rgba(9,8,6,.82) 55%, rgba(9,8,6,.55) 100%),
                url('https://tfi-3fc3.onrender.com/images/hero-1.png') center / cover no-repeat,
                #090806;
            border: 1px solid rgba(243,217,139,.25);
            box-shadow: var(--shadow-lg);
        }
        .hero::before { content: ''; position: absolute; z-index: -1; width: 480px; height: 480px; border-radius: 50%; right: -140px; top: -200px; background: radial-gradient(circle, rgba(212,168,58,.45), transparent 65%); }
        .hero::after { content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 3px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright), var(--gold-deep)); }
        .eyebrow { display: inline-flex; align-items: center; gap: .55rem; font-size: .68rem; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: var(--gold-light); margin-bottom: .9rem; }
        .eyebrow::before { content: ''; width: 28px; height: 2px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright)); }
        .hero h2 { font-size: clamp(1.7rem, 3.2vw, 2.6rem); text-transform: uppercase; color: #fff; display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
        .hero h2 .hl { background: linear-gradient(95deg, var(--gold-bright), var(--gold) 60%, var(--gold-light)); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
        .hero h2 .flame { color: var(--gold-light); width: 34px; height: 34px; stroke-width: 1.7; }
        .hero p { color: rgba(245,240,223,.78); margin-top: .8rem; max-width: 520px; line-height: 1.65; }
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

        /* ───────── Quick actions ───────── */
        .quick { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .qa {
            display: flex; align-items: center; gap: .9rem; padding: 1rem 1.1rem; text-align: left;
            background: var(--surface); border: 1px solid var(--line-strong); border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm); transition: all .25s;
        }
        .qa:hover { transform: translateY(-4px); border-color: var(--gold); box-shadow: var(--shadow-md); }
        .qa .tile { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; flex: none; color: var(--gold-text); background: rgba(201,154,46,.14); transition: all .25s; }
        .qa:hover .tile { color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold)); }
        .qa b { display: block; font-size: .9rem; font-weight: 600; }
        .qa span { font-size: .75rem; color: var(--text-muted); }

        /* ───────── Stats ───────── */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .stat {
            position: relative; overflow: hidden;
            padding: 1.25rem 1.3rem 1.1rem;
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
        .chip { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .6rem; border-radius: 100px; font-size: .68rem; font-weight: 700; letter-spacing: .04em; color: var(--gold-text); background: rgba(201,154,46,.14); border: 1px solid rgba(201,154,46,.35); }
        .chip .ic { width: 12px; height: 12px; stroke-width: 2.6; }
        .stat-value { font-family: "Oswald", sans-serif; font-size: 2.4rem; font-weight: 600; line-height: 1; color: var(--text); }
        .stat-value small { font-size: 1rem; font-weight: 500; color: var(--text-muted); margin-left: .2rem; }
        .stat-label { margin-top: .5rem; font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--text-muted); }
        .spark { display: block; width: 100%; height: 30px; margin-top: .9rem; overflow: visible; }
        .spark path { fill: none; stroke: var(--gold); stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; vector-effect: non-scaling-stroke; }

        /* ───────── Cards ───────── */
        .grid-2 { display: grid; grid-template-columns: 1.25fr 1fr; gap: 1.4rem; align-items: start; }
        .card { background: var(--surface); border: 1px solid var(--line-strong); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 1.5rem; }
        .card-head { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.2rem; }
        .card-head h3 { font-size: 1.1rem; text-transform: uppercase; display: flex; align-items: center; gap: .6rem; }
        .card-head h3 .ic { color: var(--gold-text); }
        .link { display: inline-flex; align-items: center; gap: .35rem; font-size: .82rem; font-weight: 600; color: var(--gold-text); transition: gap .2s; }
        .link:hover { gap: .6rem; text-decoration: underline; text-underline-offset: 3px; }
        .link .ic { width: 15px; height: 15px; }

        /* plan */
        .plan { position: relative; overflow: hidden; background: radial-gradient(70% 140% at 100% 0%, rgba(201,154,46,.18), transparent 60%), var(--surface); border-color: var(--gold); }
        .plan-top { display: flex; align-items: center; gap: 1.2rem; }
        .plan-ic { width: 64px; height: 64px; border-radius: 16px; flex: none; display: grid; place-items: center; color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep)); box-shadow: 0 12px 26px var(--primary-glow); }
        .plan-ic .ic { width: 30px; height: 30px; }
        .plan-top h3 { font-size: 1.35rem; text-transform: uppercase; }
        .plan-top p { font-size: .88rem; color: var(--text-muted); margin-top: .3rem; }
        .badge-active { margin-left: auto; display: inline-flex; align-items: center; gap: .45rem; padding: .4rem .95rem; border-radius: 100px; font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--gold-text); background: rgba(201,154,46,.14); border: 1px solid rgba(201,154,46,.45); flex: none; }
        .badge-active::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); animation: pulse 2s infinite; }
        @keyframes pulse { 50% { opacity: .35; } }
        .phases { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; margin-top: 1.5rem; }
        .phase { padding-top: .7rem; border-top: 4px solid var(--soft); font-size: .7rem; font-weight: 600; color: var(--text-muted); line-height: 1.35; }
        .phase b { display: block; font-size: .62rem; letter-spacing: .16em; text-transform: uppercase; opacity: .8; margin-bottom: .15rem; }
        .phase.done { border-color: var(--gold-deep); color: var(--text); }
        .phase.now  { border-color: var(--gold-bright); color: var(--gold-text); }
        .phase.now b::after { content: ' • now'; color: var(--gold); }

        /* progress */
        .prog-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: .55rem; }
        .prog-row span { font-size: .82rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .08em; }
        .prog-row b { font-family: "Oswald", sans-serif; font-size: 1.15rem; font-weight: 600; color: var(--gold-text); }
        .bar-wrap { height: 10px; background: var(--soft); border-radius: 100px; overflow: hidden; margin-bottom: 1.15rem; }
        .bar-fill { height: 100%; border-radius: 100px; background: linear-gradient(90deg, var(--gold-deep), var(--gold) 50%, var(--gold-bright)); box-shadow: 0 0 12px var(--primary-glow); transform-origin: left; animation: grow 1.2s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes grow { from { width: 0 !important; } }
        .prog-note { font-size: .8rem; color: var(--text-muted); margin: -.6rem 0 1.4rem; }

        .coach-card { display: grid; grid-template-columns: auto 1fr; gap: 1.2rem 1.4rem; align-items: center; }
        .coach-photo { width: 96px; height: 96px; font-size: 1.85rem; border-radius: 22px; box-shadow: 0 12px 26px var(--primary-glow); }
        .coach-card h3 { font-size: 1.45rem; text-transform: uppercase; margin-top: .2rem; }
        .coach-card .role { font-size: .68rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-text); }
        .coach-contacts { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: .9rem; }
        .coach-contacts a {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .45rem .8rem; border-radius: 100px;
            font-size: .82rem; font-weight: 600; color: var(--gold-text);
            background: rgba(201,154,46,.12); border: 1px solid rgba(201,154,46,.35);
        }
        .coach-contacts a:hover { background: linear-gradient(135deg, var(--gold-bright), var(--gold)); color: #17130a; border-color: transparent; }
        .coach-contacts .ic { width: 15px; height: 15px; }
        @media (max-width: 720px) { .coach-card { grid-template-columns: 1fr; justify-items: start; } }
        .flash { padding: 1rem 1.2rem; border-radius: 12px; font-size: .92rem; font-weight: 600; }
        .flash-ok { color: #11633c; background: rgba(22,130,80,.10); border: 1px solid rgba(22,130,80,.35); }
        html.dark-mode .flash-ok { color: #6ee7b7; background: rgba(52,211,153,.10); border-color: rgba(52,211,153,.35); }
        .flash-err { color: #a11d1d; background: rgba(200,40,40,.08); border: 1px solid rgba(200,40,40,.30); }
        .msg-form { display: grid; gap: .8rem; }
        .msg-form textarea {
            width: 100%; min-height: 110px; padding: .9rem 1rem; border-radius: 12px; resize: vertical;
            border: 1px solid var(--line-strong); background: var(--bg); color: var(--text); font: inherit;
        }
        .msg-form textarea:focus { outline: 2px solid var(--gold); outline-offset: 1px; }
        .thread { list-style: none; display: grid; gap: .7rem; margin-top: 1.1rem; }
        .thread li { padding: .85rem 1rem; border-radius: 12px; background: var(--bg); border: 1px solid var(--line); }
        .thread li.from-trainer { border-color: rgba(201,154,46,.35); background: rgba(201,154,46,.08); }
        .file-list { list-style: none; display: grid; gap: .65rem; margin-top: .4rem; }
        .file-list li { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: .85rem 1rem; border-radius: 12px; background: var(--bg); border: 1px solid var(--line); }
        .thread b { display: block; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; color: var(--gold-text); margin-bottom: .35rem; }
        .thread p { font-size: .9rem; line-height: 1.5; white-space: pre-wrap; }

        /* activity chart */
        .chart { display: grid; grid-template-columns: repeat(7, 1fr); gap: .6rem; align-items: end; height: 150px; margin-top: .4rem; }
        .col { display: grid; justify-items: center; align-content: end; gap: .45rem; height: 100%; }
        .col .b { width: 100%; max-width: 34px; border-radius: 8px 8px 4px 4px; min-height: 6px; background: var(--soft); transition: transform .25s; }
        .col.on .b { background: linear-gradient(180deg, var(--gold-bright), var(--gold) 55%, var(--gold-deep)); box-shadow: 0 8px 16px var(--primary-glow); }
        .col:hover .b { transform: translateY(-3px); }
        .col span { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text-muted); }
        .col.today span { color: var(--gold-text); }
        .col.today span::after { content: ''; display: block; width: 5px; height: 5px; border-radius: 50%; background: var(--gold); margin: .2rem auto 0; }

        /* schedule */
        .sched { list-style: none; display: grid; gap: .3rem; }
        .sess { display: flex; align-items: center; gap: .95rem; padding: .85rem; border-radius: 14px; border: 1px solid transparent; transition: all .2s; }
        .sess:hover { background: rgba(201,154,46,.08); border-color: rgba(201,154,46,.3); }
        .sess .tile { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; flex: none; color: var(--gold-text); background: rgba(201,154,46,.14); }
        .sess.first .tile { color: #17130a; background: linear-gradient(135deg, var(--gold-bright), var(--gold)); box-shadow: 0 8px 16px var(--primary-glow); }
        .sess-info { flex: 1; min-width: 0; }
        .sess-info b { display: block; font-size: .92rem; font-weight: 600; }
        .sess-info span { font-size: .76rem; color: var(--text-muted); }
        .day { font-size: .66rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .28rem .65rem; border-radius: 100px; color: var(--text-muted); background: var(--soft); white-space: nowrap; }
        .sess.first .day { color: var(--gold-text); background: rgba(201,154,46,.14); border: 1px solid rgba(201,154,46,.4); }

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

        .hero p strong { color: var(--gold-bright); font-weight: 600; }
        .empty-card { text-align: center; padding: 2.2rem 1.5rem; }
        .empty-card h3 { font-size: 1.25rem; text-transform: uppercase; }
        .empty-card p { margin-top: .5rem; color: var(--text-muted); font-size: .9rem; }
        .sess-empty { padding: 1.4rem .5rem; text-align: center; color: var(--text-muted); font-size: .88rem; }

        .overlay { display: none; }

        /* ───────── Responsive ───────── */
        @media (max-width: 1180px) {
            .stats, .quick { grid-template-columns: repeat(2, 1fr); }
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
            .plan-top { flex-wrap: wrap; }
            .badge-active { margin-left: 0; }
            .page-title h1 { font-size: 1.2rem; }
            .page-title p { display: none; }
        }
        @media (max-width: 520px) {
            .stats, .quick { grid-template-columns: 1fr 1fr; gap: .7rem; }
            .stat { padding: 1rem; }
            .stat-value { font-size: 1.9rem; }
            .qa { flex-direction: column; align-items: flex-start; gap: .6rem; }
            .phases { grid-template-columns: repeat(2, 1fr); row-gap: 1rem; }
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
    <symbol id="i-dumbbell" viewBox="0 0 24 24"><path d="M6.5 6.5v11M17.5 6.5v11M3 9v6M21 9v6M6.5 12h11"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
    <symbol id="i-trend" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/></symbol>
    <symbol id="i-leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.5 2 8 0 5.5-4.8 10-10 10z"/><path d="M2 21c0-3 1.9-5.5 6-7"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24"><path d="M6.5 3h3l1.5 4-2 1.5a12 12 0 0 0 6 6L17 13l4 1.5v3A16 16 0 0 1 6.5 3z"/></symbol>
    <symbol id="i-card" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20M6 15h4"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></symbol>
    <symbol id="i-flame" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-3-1.5-5-3-6.5-.5 1.5-1.5 2.5-2.5 3 .5-3-.5-6-3.5-8.5C9.5 7 5 10 5 15a7 7 0 0 0 7 7z"/><path d="M12 22a3 3 0 0 0 3-3c0-1.5-1-2.5-3-4-2 1.5-3 2.5-3 4a3 3 0 0 0 3 3z"/></symbol>
    <symbol id="i-timer" viewBox="0 0 24 24"><path d="M10 2h4"/><path d="M12 14l3-3"/><circle cx="12" cy="14" r="8"/></symbol>
    <symbol id="i-drop" viewBox="0 0 24 24"><path d="M12 22a7 7 0 0 0 7-7c0-4-7-13-7-13S5 11 5 15a7 7 0 0 0 7 7z"/></symbol>
    <symbol id="i-trophy" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16M10 14.7V17c0 .6-.5 1-1.1 1.2C7.9 18.8 7 20.2 7 22M14 14.7V17c0 .6.5 1 1.1 1.2 1 .6 1.9 2 1.9 3.8"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></symbol>
    <symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></symbol>
    <symbol id="i-up-right" viewBox="0 0 24 24"><path d="M7 17L17 7M8 7h9v9"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></symbol>
    <symbol id="i-activity" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></symbol>
    <symbol id="i-yoga" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.8"/><path d="M12 8v5M7.5 10.5L12 8l4.5 2.5M9.5 21l2.5-8 2.5 8"/></symbol>
    <symbol id="i-zap" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></symbol>
    <symbol id="i-run" viewBox="0 0 24 24"><circle cx="14" cy="4.5" r="1.8"/><path d="M5 12l3-3 4 1 2 3 3 1M9 21l2-6-2.5-2.5M14 14l1.5 3.5L19 19"/></symbol>
    <symbol id="i-msg" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-play" viewBox="0 0 24 24"><path d="M6 4l14 8-14 8z"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="M5 12l5 5 9-10"/></symbol>
</svg>

<div class="app">

    <!-- ═════════ Sidebar ═════════ -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ $websiteUrl }}" class="brand" aria-label="TFI — Time to Fit India">
            <img src="{{ $websiteUrl }}logo/logo.png" alt="TFI"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" />
            <span class="brand-fallback">TFI</span>
            <span class="brand-text">TFI Portal<small>My Fitness Portal</small></span>
        </a>

        <div class="user-card">
            <div class="avatar">
                @if ($user->avatarUrl())
                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="user-meta">
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-role">Trainee</div>
            </div>
            <span class="online" title="Online"></span>
        </div>

        <nav class="nav">
            @include('dashboard.partials.portal-nav')
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
                <h1>My Dashboard</h1>
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
                <a href="#" class="icon-btn" aria-label="Notifications"><svg class="ic"><use href="#i-bell"/></svg><span class="dot"></span></a>
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost btn-logout hide-sm"><svg class="ic"><use href="#i-logout"/></svg><span>Logout</span></button>
                    </form>
                @endif
            </div>
        </header>

        <main class="content">
            @if (session('success'))
                <div class="flash flash-ok">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-err">{{ $errors->first() }}</div>
            @endif

            <!-- Hero -->
            <section class="hero">
                <div>
                    <div class="eyebrow">{{ $greet }}</div>
                    <h2>
                        <span>Hey <span class="hl">{{ $user->name }}</span>, {{ $streak > 0 ? 'keep pushing!' : "let's get started!" }}</span>
                        <svg class="ic flame"><use href="#i-flame"/></svg>
                    </h2>
                    <p>
                        @if ($trainer)
                            Your trainer is <strong>{{ $trainer }}</strong>.
                        @endif
                        @if ($streak > 0)
                            You're on a <strong>{{ $streak }}-day streak</strong>. Keep it going today.
                        @elseif ($planName)
                            Your plan is ready. Log your first workout to start a streak.
                        @else
                            Choose a program to begin your fitness journey with TFI.
                        @endif
                    </p>
                    <div class="hero-cta">
                        @if ($planName)
                            <a href="{{ route('customer.schedule') }}" class="btn-gold"><svg class="ic"><use href="#i-play"/></svg> {{ $nextToday ? "Start today's session" : 'View schedule' }}</a>
                        @else
                            <a href="{{ url('/#programs') }}" class="btn-gold"><svg class="ic"><use href="#i-dumbbell"/></svg> Browse programs</a>
                        @endif
                        <a href="{{ $websiteUrl }}" class="btn-ghost"><svg class="ic"><use href="#i-globe"/></svg> Visit website</a>
                    </div>
                </div>

                <div class="ring-card">
                    <div class="ring" style="--off: {{ $ringOff }};">
                        <svg viewBox="0 0 120 120"><circle class="track" cx="60" cy="60" r="52"/><circle class="bar" cx="60" cy="60" r="52"/></svg>
                        <div class="ring-val"><b>{{ $weekPct }}<i>%</i></b></div>
                    </div>
                    <div class="ring-cap">
                        Weekly goal<br>
                        @if ($goal > 0) {{ $done }} of {{ $goal }} sessions @else No goal set @endif
                    </div>
                </div>
            </section>

            <section class="quick">
                <a href="{{ route('customer.program') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-dumbbell"/></svg></span><div><b>My Program</b><span>See your current plan</span></div></a>
                <a href="{{ route('customer.messages.index') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-msg"/></svg></span><div><b>Message coach</b><span>Get expert guidance</span></div></a>
                <a href="{{ route('customer.nutrition') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-leaf"/></svg></span><div><b>Nutrition plan</b><span>Documents from your trainer</span></div></a>
                <a href="{{ route('customer.schedule') }}" class="qa"><span class="tile"><svg class="ic"><use href="#i-calendar"/></svg></span><div><b>Schedule</b><span>Upcoming sessions</span></div></a>
            </section>

            <section class="stats">
                <article class="stat">
                    <div class="stat-top">
                        <span class="tile-ic"><svg class="ic"><use href="#i-flame"/></svg></span>
                        @if ($streak >= 7)<span class="chip"><svg class="ic"><use href="#i-up"/></svg>On fire</span>@endif
                    </div>
                    <div class="stat-value">{{ $streak }}<small>{{ $streak === 1 ? 'day' : 'days' }}</small></div>
                    <div class="stat-label">Day streak</div>
                </article>
                <article class="stat">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-timer"/></svg></span></div>
                    <div class="stat-value">{{ $fmt($weekHours) }}<small>hrs</small></div>
                    <div class="stat-label">This week</div>
                </article>
                <article class="stat">
                    <div class="stat-top">
                        <span class="tile-ic"><svg class="ic"><use href="#i-drop"/></svg></span>
                        @if ($hydrationGoal > 0 && $hydration >= $hydrationGoal)<span class="chip"><svg class="ic"><use href="#i-check"/></svg>Goal met</span>@endif
                    </div>
                    <div class="stat-value">{{ $fmt($hydration) }}<small>L</small></div>
                    <div class="stat-label">Hydration today</div>
                </article>
                <article class="stat">
                    <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-trophy"/></svg></span></div>
                    <div class="stat-value">{{ $badges }}</div>
                    <div class="stat-label">Badges earned</div>
                </article>
            </section>

            <!-- Website CTA -->
            <section class="site-cta">
                <div>
                    <h3>Explore more on the <span>TFI website</span></h3>
                    <p>Programs, trainers, specialized plans and more — all on our main site.</p>
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
