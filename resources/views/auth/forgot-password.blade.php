<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password — TFI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; padding: 24px; display: grid; place-items: center; background: #0f0f1a; color: #e2e8f0; font: 15px Inter, sans-serif; }
        main { width: min(100%, 440px); padding: 36px; border: 1px solid #ffffff20; border-radius: 22px; background: #ffffff0a; box-shadow: 0 24px 70px #0008; }
        h1 { margin: 0 0 10px; text-align: center; } p { color: #94a3b8; line-height: 1.6; text-align: center; }
        label { display: block; margin: 22px 0 7px; color: #cbd5e1; font-size: 13px; font-weight: 600; }
        input { width: 100%; padding: 13px; border: 1px solid #ffffff24; border-radius: 10px; background: #ffffff0a; color: white; font: inherit; }
        button { width: 100%; margin-top: 20px; padding: 14px; border: 0; border-radius: 10px; background: linear-gradient(120deg,#7c3aed,#0ea5e9); color: white; font: inherit; font-weight: 700; cursor: pointer; }
        a { color: #c4b5fd; text-decoration: none; } .alert { margin: 16px 0; padding: 12px; border-radius: 9px; background: #10b98122; color: #a7f3d0; }
        .error { color: #fca5a5; font-size: 13px; }
    </style>
</head>
<body>
<main>
    <h1>Forgot your password?</h1>
    <p>Enter your account email. If it matches an account, we’ll send a password reset link.</p>

    @if (session('status'))
        <div class="alert" role="status">{{ session('status') }}</div>
    @endif
    @error('email') <div class="alert error" role="alert">{{ $message }}</div> @enderror

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
        <button type="submit">Send reset link</button>
    </form>
    <p><a href="{{ route('login') }}">Back to sign in</a></p>
</main>
</body>
</html>
