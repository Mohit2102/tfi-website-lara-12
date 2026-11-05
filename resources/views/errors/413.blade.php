<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload too large</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; font-family: system-ui, sans-serif; background: #f6f0e2; color: #14130f; }
        .box { max-width: 460px; padding: 32px; border-radius: 18px; background: #fffdf8; border: 1px solid rgba(184, 137, 43, .35); box-shadow: 0 18px 40px rgba(20, 19, 15, .1); }
        h1 { margin: 0 0 12px; font-size: 22px; }
        p { margin: 0 0 20px; line-height: 1.6; color: #3a362c; }
        a { display: inline-block; padding: 12px 22px; border-radius: 999px; background: linear-gradient(135deg, #f1d488, #b8892b); color: #14120e; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Those files are too large to upload together</h1>
        <p>Nothing was saved. Go back and upload fewer images at a time, keeping each image under {{ \App\Support\ImageUpload::MAX_KB / 1024 }} MB. Images are converted to WebP automatically after upload.</p>
        <a href="{{ url()->previous() }}">Go back</a>
    </div>
</body>
</html>
