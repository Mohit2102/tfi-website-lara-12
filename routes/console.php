<?php

use App\Support\WebpImage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:webp {--force : Rebuild copies that are already up to date}', function () {
    ini_set('memory_limit', '1024M');

    $made = 0;

    foreach (WebpImage::DIRECTORIES as $directory) {
        $root = public_path($directory);

        if (! is_dir($root)) {
            continue;
        }

        $maxWidth = $directory === 'logo' ? 480 : WebpImage::MAX_WIDTH;
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            $path = $file->getPathname();

            if (! preg_match('/\.(png|jpe?g)$/i', $path)) {
                continue;
            }

            $webp = WebpImage::pathFor($path);

            if (! $this->option('force') && is_file($webp) && filemtime($webp) >= filemtime($path)) {
                continue;
            }

            if (! WebpImage::create($path, $maxWidth)) {
                $this->warn('Skipped '.$path);

                continue;
            }

            $this->line(sprintf(
                '%s  %s KB -> %s KB',
                str_replace(public_path().DIRECTORY_SEPARATOR, '', $path),
                number_format(filesize($path) / 1024),
                number_format(filesize($webp) / 1024)
            ));
            $made++;
        }
    }

    $this->info($made.' WebP image(s) written.');
})->purpose('Create resized WebP copies of public images (served automatically on site pages)');
