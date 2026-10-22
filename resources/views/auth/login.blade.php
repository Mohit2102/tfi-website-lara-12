<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>Login — TFI Portal</title>
    <meta name="description" content="Sign in to the TFI fitness portal. Access your personalized dashboard as a superadmin, admin, or customer." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet" />
    <script>
        // Match the main website's dark-mode class if the visitor chose it there
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
            --text-secondary: #2e2103;
            --text-muted: #5b5240;

            --line: rgba(23, 19, 10, 0.10);
            --line-strong: rgba(23, 19, 10, 0.18);
            --primary-glow: rgba(201, 154, 46, 0.30);

            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 18px;

            --shadow-sm: 0 8px 24px rgba(23, 19, 10, 0.06);
            --shadow-md: 0 18px 40px rgba(23, 19, 10, 0.11);
            --shadow-lg: 0 24px 60px rgba(23, 19, 10, 0.18);

            --ok-bg: rgba(22, 130, 80, .10);  --ok-line: rgba(22, 130, 80, .35);  --ok-text: #11633c;
            --err-bg: rgba(200, 40, 40, .08); --err-line: rgba(200, 40, 40, .35); --err-text: #a11d1d;

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
            --text-secondary: #fafafa;
            --text-muted: #b9b2a0;

            --line: rgba(245, 240, 223, 0.10);
            --line-strong: rgba(245, 240, 223, 0.20);
            --primary-glow: rgba(212, 168, 58, 0.34);

            --ok-bg: rgba(52, 211, 153, .10);  --ok-line: rgba(52, 211, 153, .35);  --ok-text: #6ee7b7;
            --err-bg: rgba(239, 68, 68, .10);  --err-line: rgba(239, 68, 68, .35);  --err-text: #fca5a5;

            color-scheme: dark;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            padding-top: calc(1.5rem + env(safe-area-inset-top, 0px));
            padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px));
            background: var(--bg);
            color: var(--text);
            font-family: "Inter", system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            overflow-x: hidden;
            position: relative;
            transition: background-color .35s ease, color .35s ease;
        }

        h1, h2, h3, .disp { font-family: "Oswald", sans-serif; font-weight: 600; line-height: 1.06; letter-spacing: .005em; }
        a { color: inherit; text-decoration: none; }

        /* ───────── Page background ───────── */
        .bg {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(55% 45% at 8% 0%,   var(--primary-glow), transparent 65%),
                radial-gradient(45% 40% at 100% 100%, var(--primary-glow), transparent 65%),
                var(--bg);
        }
        .bg::after {
            content: ''; position: absolute; inset: 0;
            background-image:
                linear-gradient(var(--line) 1px, transparent 1px),
                linear-gradient(90deg, var(--line) 1px, transparent 1px);
            background-size: 64px 64px;
            opacity: .55;
            mask-image: radial-gradient(ellipse at center, #000 20%, transparent 72%);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 20%, transparent 72%);
        }

        /* ───────── Shell ───────── */
        .shell {
            position: relative; z-index: 2;
            width: 100%; max-width: 1060px;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            background: var(--surface);
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: rise .75s cubic-bezier(.16, 1, .3, 1);
        }
        @keyframes rise {
            from { opacity: 0; transform: translateY(26px) scale(.985); }
            to   { opacity: 1; transform: none; }
        }

        /* ───────── Brand panel (always dark + gold) ───────── */
        .brand {
            position: relative;
            padding: 2.75rem 2.6rem;
            display: flex; flex-direction: column; justify-content: space-between;
            color: #f5f0df;
            background:
                linear-gradient(160deg, rgba(9,8,6,.78) 0%, rgba(9,8,6,.90) 60%, rgba(18,14,6,.98) 100%),
                url('https://tfi-3fc3.onrender.com/images/hero-1.png') center / cover no-repeat,
                #090806;
            overflow: hidden;
            isolation: isolate;
        }
        .brand::before {
            content: ''; position: absolute; z-index: -1;
            width: 520px; height: 520px; border-radius: 50%;
            right: -220px; top: -220px;
            background: radial-gradient(circle, rgba(212,168,58,.38), transparent 65%);
        }
        .brand::after {
            content: ''; position: absolute; z-index: -1;
            left: 0; right: 0; bottom: 0; height: 3px;
            background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright), var(--gold-deep));
        }

        .logo { display: inline-flex; align-items: center; gap: .8rem; margin-bottom: 2.4rem; }
        .logo img { height: 46px; width: auto; display: block; }
        .logo-fallback {
            width: 46px; height: 46px; border-radius: var(--radius-md);
            display: none; place-items: center;
            font-family: "Oswald", sans-serif; font-weight: 700; font-size: 1.15rem; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold) 60%, var(--gold-deep));
        }
        .logo-text { font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1.05rem; letter-spacing: .06em; text-transform: uppercase; line-height: 1.1; }
        .logo-text small { display: block; font-family: "Inter", sans-serif; font-size: .64rem; font-weight: 600; letter-spacing: .2em; color: var(--gold-light); margin-top: .2rem; }

        .eyebrow {
            display: inline-flex; align-items: center; gap: .55rem;
            font-size: .68rem; font-weight: 600; letter-spacing: .22em; text-transform: uppercase;
            color: var(--gold-light); margin-bottom: 1rem;
        }
        .eyebrow::before { content: ''; width: 28px; height: 2px; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright)); }

        .brand h2 {
            font-size: clamp(2rem, 3.6vw, 3rem);
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 1rem;
        }
        .brand h2 span {
            background: linear-gradient(95deg, var(--gold-bright), var(--gold) 55%, var(--gold-light));
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
        }
        .brand .lead { color: rgba(245,240,223,.78); font-size: .95rem; line-height: 1.7; max-width: 380px; }

        /* TFI method steps */
        .method { margin-top: 2rem; }
        .method-title { font-size: .66rem; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; color: rgba(245,240,223,.55); margin-bottom: .8rem; }
        .steps { display: flex; flex-wrap: wrap; gap: .5rem; }
        .step {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .45rem .8rem .45rem .5rem;
            border: 1px solid rgba(243,217,139,.28);
            background: rgba(243,217,139,.07);
            border-radius: 100px;
            font-family: "Oswald", sans-serif; font-weight: 500; font-size: .78rem; letter-spacing: .1em; text-transform: uppercase;
            color: var(--gold-bright);
            transition: transform .25s, background .25s, border-color .25s;
        }
        .step i {
            font-style: normal; width: 22px; height: 22px; border-radius: 50%;
            display: grid; place-items: center; font-family: "Inter", sans-serif; font-size: .64rem; font-weight: 700; color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright), var(--gold));
        }
        .step:hover { transform: translateY(-2px); background: rgba(243,217,139,.14); border-color: rgba(243,217,139,.5); }

        .brand-quote {
            margin-top: 2rem; padding-top: 1.4rem;
            border-top: 1px solid rgba(245,240,223,.14);
            font-family: "Oswald", sans-serif; font-weight: 500; font-size: 1.1rem; letter-spacing: .02em; line-height: 1.3;
            color: #fff;
        }
        .brand-quote span { color: var(--gold-light); }

        /* ───────── Form panel ───────── */
        .panel {
            padding: 2.9rem 2.7rem;
            display: flex; flex-direction: column; justify-content: center;
            background: var(--surface);
        }

        .back-link {
            align-self: flex-start;
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .78rem; font-weight: 600; color: var(--text-muted);
            margin-bottom: 1.6rem; transition: color .2s, gap .2s;
        }
        .back-link:hover { color: var(--gold-deep); gap: .6rem; }
        html.dark-mode .back-link:hover { color: var(--gold-light); }
        .back-link svg { width: 15px; height: 15px; }

        .panel-head { margin-bottom: 1.25rem; }
        .panel-head .eyebrow { color: var(--gold-deep); margin-bottom: .7rem; }
        html.dark-mode .panel-head .eyebrow { color: var(--gold-light); }
        .panel-head h1 { font-size: 2.3rem; text-transform: uppercase; color: var(--text); }
        .panel-head h1 span { color: var(--gold); }
        .panel-head p { color: var(--text-muted); font-size: .92rem; margin-top: .55rem; }

        /* role badges */
        .role-badges { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.6rem; }
        .role-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .28rem .7rem; border-radius: 100px;
            font-size: .64rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
            color: var(--gold-deep);
            background: rgba(201,154,46,.10);
            border: 1px solid rgba(201,154,46,.35);
        }
        html.dark-mode .role-badge { color: var(--gold-light); background: rgba(212,168,58,.10); border-color: rgba(212,168,58,.35); }
        .role-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); }

        /* alerts */
        .alert {
            display: flex; gap: .6rem; align-items: flex-start;
            padding: .85rem 1rem; border-radius: var(--radius-md); margin-bottom: 1.2rem;
            font-size: .85rem; line-height: 1.45; animation: rise .4s ease;
        }
        .alert svg { width: 18px; height: 18px; flex: none; margin-top: 1px; }
        .alert-success { background: var(--ok-bg);  border: 1px solid var(--ok-line);  color: var(--ok-text); }
        .alert-error   { background: var(--err-bg); border: 1px solid var(--err-line); color: var(--err-text); }

        /* form */
        .form-group { margin-bottom: 1.15rem; }
        label {
            display: block; margin-bottom: .5rem;
            font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
            color: var(--text-muted);
        }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: var(--text-muted); opacity: .75;
            pointer-events: none; transition: color .2s, opacity .2s;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            padding: .95rem 1rem .95rem 2.85rem;
            background: var(--bg);
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            color: var(--text); font: inherit; font-size: .93rem;
            outline: none; transition: border-color .25s, box-shadow .25s, background .25s;
        }
        input::placeholder { color: var(--text-muted); opacity: .55; }
        input:hover { border-color: var(--gold); }
        input:focus {
            border-color: var(--gold);
            background: var(--surface);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }
        .input-wrapper:focus-within .input-icon { color: var(--gold-deep); opacity: 1; }
        html.dark-mode .input-wrapper:focus-within .input-icon { color: var(--gold-light); }
        .input-error { border-color: var(--err-line) !important; background: var(--err-bg) !important; }
        .field-error { display: flex; align-items: center; gap: .35rem; margin-top: .45rem; font-size: .78rem; color: var(--err-text); }

        .eye-toggle {
            position: absolute; right: .55rem; top: 50%; transform: translateY(-50%);
            width: 36px; height: 36px; border-radius: var(--radius-md);
            display: grid; place-items: center;
            background: none; border: none; cursor: pointer; color: var(--text-muted);
            transition: all .2s;
        }
        .eye-toggle:hover { color: var(--gold-deep); background: rgba(201,154,46,.12); }
        html.dark-mode .eye-toggle:hover { color: var(--gold-light); }
        .eye-toggle svg { width: 18px; height: 18px; }

        .form-extras { display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin: .2rem 0 1.5rem; }
        .remember-label {
            display: flex; align-items: center; gap: .55rem; cursor: pointer;
            margin: 0; font-size: .85rem; font-weight: 500; letter-spacing: 0; text-transform: none; color: var(--text-muted);
        }
        .remember-label input { width: 17px; height: 17px; accent-color: var(--gold); cursor: pointer; }
        .forgot-link { font-size: .85rem; font-weight: 600; color: var(--gold-deep); transition: color .2s; }
        html.dark-mode .forgot-link { color: var(--gold-light); }
        .forgot-link:hover { color: var(--gold-dark); text-decoration: underline; text-underline-offset: 3px; }
        html.dark-mode .forgot-link:hover { color: var(--gold-bright); }

        .btn-login {
            position: relative; overflow: hidden;
            width: 100%; padding: 1rem;
            border: none; border-radius: var(--radius-md); cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            font-family: "Oswald", sans-serif; font-weight: 600; font-size: 1.02rem; letter-spacing: .14em; text-transform: uppercase;
            color: #17130a;
            background: linear-gradient(135deg, var(--gold-bright) 0%, var(--gold-light) 30%, var(--gold) 65%, var(--gold-deep) 100%);
            background-size: 180% 100%; background-position: 0% 0;
            box-shadow: 0 12px 28px var(--primary-glow), inset 0 1px 0 rgba(255,255,255,.45);
            transition: transform .3s ease, box-shadow .3s ease, background-position .5s ease;
        }
        .btn-login::after {
            content: ''; position: absolute; top: 0; left: -120%; width: 55%; height: 100%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.5), transparent);
            transform: skewX(-20deg); transition: left .65s;
        }
        .btn-login:hover { transform: translateY(-2px); background-position: 100% 0; box-shadow: 0 18px 38px var(--primary-glow), inset 0 1px 0 rgba(255,255,255,.5); }
        .btn-login:hover::after { left: 140%; }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { cursor: wait; }
        .btn-login svg { width: 18px; height: 18px; transition: transform .25s; }
        .btn-login:hover svg { transform: translateX(4px); }

        .auth-switch { margin-top: 1.5rem; text-align: center; font-size: .88rem; color: var(--text-muted); }
        .auth-switch a {
            color: var(--text); font-weight: 700;
            border-bottom: 2px solid var(--gold); padding-bottom: 1px;
            transition: color .2s, border-color .2s;
        }
        .auth-switch a:hover { color: var(--gold-deep); border-color: var(--gold-deep); }
        html.dark-mode .auth-switch a:hover { color: var(--gold-light); border-color: var(--gold-light); }

        .secure { margin-top: 1.4rem; display: flex; align-items: center; justify-content: center; gap: .45rem; font-size: .72rem; color: var(--text-muted); opacity: .8; }
        .secure svg { width: 14px; height: 14px; color: var(--gold); }

        /* ───────── Responsive ───────── */
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; max-width: 480px; }
            .brand { padding: 1.75rem 1.6rem 1.9rem; }
            .brand .lead, .brand .method, .brand .brand-quote { display: none; }
            .brand h2 { font-size: 1.7rem; margin-bottom: 0; }
            .logo { margin-bottom: 1.2rem; }
            .panel { padding: 2rem 1.6rem 2.2rem; }
            .panel-head h1 { font-size: 1.9rem; }
        }
        @media (max-width: 380px) {
            body { padding: .75rem; }
            .panel { padding: 1.75rem 1.25rem 2rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>
    <div class="bg"></div>

    <main class="shell">

        <!-- ───── Brand panel ───── -->
        <aside class="brand">
            <div>
                <a href="{{ url('/') }}" class="logo" aria-label="TFI — Time to Fit India">
                    <img src="https://tfi-3fc3.onrender.com/logo/logo.png" alt="TFI — Time to Fit India"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';" />
                    <span class="logo-fallback">TFI</span>
                    <span class="logo-text">Time to Fit India<small>Member Portal</small></span>
                </a>

                <div class="eyebrow">Your fitness. Your goals.</div>
                <h2>Fitness that is<br><span>built around you.</span></h2>
                <p class="lead">Your plans, progress and coaching in one place. Sign in and pick up right where you left off.</p>

                <div class="method">
                    <div class="method-title">The TFI Method</div>
                    <div class="steps">
                        <span class="step"><i>1</i>Assess</span>
                        <span class="step"><i>2</i>Understand</span>
                        <span class="step"><i>3</i>Plan</span>
                        <span class="step"><i>4</i>Train</span>
                        <span class="step"><i>5</i>Progress</span>
                    </div>
                </div>
            </div>

            <p class="brand-quote">Fitness should be personal.<br><span>Training should have a purpose.</span></p>
        </aside>

        <!-- ───── Form panel ───── -->
        <section class="panel">
            <a href="{{ url('/') }}" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Back to website
            </a>

            <div class="panel-head">
                <div class="eyebrow">Member login</div>
                <h1>Welcome <span>back</span></h1>
                <p>Sign in to your account to continue your journey.</p>
            </div>

            <div class="role-badges">
                <span class="role-badge">Super Admin</span>
                <span class="role-badge">Admin</span>
                <span class="role-badge">Customer</span>
            </div>

            <!-- Flash messages -->
            @if (session('success'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-6"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                @csrf
                @if (is_string(request('redirect')))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 7l9 6 9-6"/></svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            autocomplete="email"
                            required
                            class="{{ $errors->has('email') ? 'input-error' : '' }}"
                        />
                    </div>
                    @error('email')
                        <p class="field-error"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="3"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            class="{{ $errors->has('password') ? 'input-error' : '' }}"
                        />
                        <button type="button" class="eye-toggle" id="eyeToggle" aria-label="Toggle password visibility">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error"><span>⚠</span> {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-extras">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember" />
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="loginLabel">Sign In</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <p class="auth-switch">New to TFI? <a href="{{ route('register', request()->only('redirect')) }}">Create a customer account</a></p>

            <p class="secure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="10" rx="3"/><path d="M8 11V8a4 4 0 018 0v3"/></svg>
                Your connection is encrypted &amp; secure
            </p>
        </section>
    </main>

    <script>
        // ── Eye toggle
        const eyeBtn = document.getElementById('eyeToggle');
        const eyeIcon = document.getElementById('eyeIcon');
        const pwInput = document.getElementById('password');
        const eyeOpen = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>';
        const eyeOff  = '<path d="M17.94 17.94A10.9 10.9 0 0112 19c-7 0-11-7-11-7a19.8 19.8 0 015.06-5.94M9.9 4.24A10.9 10.9 0 0112 4c7 0 11 7 11 7a19.8 19.8 0 01-3.17 4.19M1 1l22 22"/><path d="M14.12 14.12A3 3 0 019.88 9.88"/>';
        eyeBtn.addEventListener('click', () => {
            const hidden = pwInput.type === 'password';
            pwInput.type = hidden ? 'text' : 'password';
            eyeIcon.innerHTML = hidden ? eyeOff : eyeOpen;
        });

        // ── Loading state on submit
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loginLabel = document.getElementById('loginLabel');
        loginForm.addEventListener('submit', () => {
            loginLabel.textContent = 'Signing in…';
            loginBtn.disabled = true;
            loginBtn.style.opacity = '0.85';
        });
    </script>
</body>
</html>