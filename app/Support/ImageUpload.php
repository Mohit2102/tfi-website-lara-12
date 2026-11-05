<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Dashboard image uploads: one set of validation rules, and every upload is
 * stored as a resized .webp so pages stay light whatever format was chosen.
 */
class ImageUpload
{
    public const MAX_KB = 5120;

    public const MAX_PIXELS = 8000;

    public const EXTENSIONS = 'jpg,jpeg,png,webp,gif,bmp,avif';

    /** Widest image stored for page artwork (hero, cards, banners). */
    public const PAGE_WIDTH = 1920;

    /** Widest image stored for profile and trainer photos. */
    public const PHOTO_WIDTH = 800;

    /**
     * @return list<string>
     */
    public static function rules(): array
    {
        return [
            'nullable',
            'file',
            'mimes:'.self::EXTENSIONS,
            'max:'.self::MAX_KB,
            'dimensions:max_width='.self::MAX_PIXELS.',max_height='.self::MAX_PIXELS,
        ];
    }

    /**
     * @param  list<string>  $fields  validation keys, wildcards allowed (e.g. "uploads.hero.*")
     * @return array<string, string>
     */
    public static function messages(array $fields): array
    {
        $mb = self::MAX_KB / 1024;
        $type = 'Please upload a JPG, PNG, WebP, GIF, BMP or AVIF image.';
        $messages = [];

        foreach ($fields as $field) {
            $messages[$field.'.file'] = $type;
            $messages[$field.'.mimes'] = $type;
            $messages[$field.'.max'] = "Each image must be {$mb} MB or smaller — please compress or resize it and try again.";
            $messages[$field.'.dimensions'] = 'That image is too large. Use one no bigger than '.self::MAX_PIXELS.' × '.self::MAX_PIXELS.' pixels.';
            $messages[$field.'.uploaded'] = "The image could not be uploaded. Keep each file under {$mb} MB.";
        }

        return $messages;
    }

    /**
     * Save the upload under public/$directory as .webp (resized to $maxWidth)
     * and return its public-relative path. Animated GIFs, and anything GD
     * cannot read, are kept in their original format.
     */
    public static function store(UploadedFile $file, string $directory, int $maxWidth = self::PAGE_WIDTH): string
    {
        $directory = trim($directory, '/');
        $target = public_path($directory);

        if (! is_dir($target)) {
            mkdir($target, 0755, true);
        }

        $name = Str::uuid()->toString();
        $source = $file->getRealPath();

        if ($source && ! self::isAnimatedGif($source)
            && WebpImage::convert($source, $target.DIRECTORY_SEPARATOR.$name.'.webp', $maxWidth)) {
            return $directory.'/'.$name.'.webp';
        }

        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $file->move($target, $name.'.'.$extension);

        return $directory.'/'.$name.'.'.$extension;
    }

    private static function isAnimatedGif(string $path): bool
    {
        $handle = @fopen($path, 'rb');

        if (! $handle) {
            return false;
        }

        $frames = 0;
        $carry = '';

        if (fread($handle, 3) === 'GIF') {
            // Each frame starts with a graphic control extension; two or more means animated.
            while ($frames < 2 && ($chunk = fread($handle, 102400)) !== false && $chunk !== '') {
                $frames += substr_count($carry.$chunk, "\x21\xF9\x04");
                $carry = substr($chunk, -2);
            }
        }

        fclose($handle);

        return $frames > 1;
    }
}
