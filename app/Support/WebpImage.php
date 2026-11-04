<?php

namespace App\Support;

use GdImage;

class WebpImage
{
    /** Folders under public/ whose images get a .webp copy and are rewritten in page HTML. */
    public const DIRECTORIES = ['images', 'logo', 'uploads'];

    public const MAX_WIDTH = 1920;

    public const QUALITY = 80;

    /** @var array<string, bool> */
    private static array $available = [];

    public static function pathFor(string $file): string
    {
        return (string) preg_replace('/\.(png|jpe?g)$/i', '.webp', $file);
    }

    /**
     * Write a resized .webp next to $file. Returns false when GD cannot read it.
     */
    public static function create(string $file, int $maxWidth = self::MAX_WIDTH): bool
    {
        if (! function_exists('imagewebp') || ! is_file($file)) {
            return false;
        }

        $info = @getimagesize($file);

        if (! $info) {
            return false;
        }

        $image = match ($info[2]) {
            IMAGETYPE_PNG => @imagecreatefrompng($file),
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file),
            default => false,
        };

        if (! $image instanceof GdImage) {
            return false;
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $image = self::orient($image, $file);
        }

        imagepalettetotruecolor($image);

        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth) {
            $newHeight = (int) round($height * $maxWidth / $width);
            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $written = imagewebp($image, self::pathFor($file), self::QUALITY);
        imagedestroy($image);
        self::$available = [];

        return $written;
    }

    /**
     * Point local png/jpg references at their .webp copy when one exists and is up to date.
     */
    public static function rewrite(string $html): string
    {
        $folders = implode('|', self::DIRECTORIES);

        return preg_replace_callback(
            '#(["\'(]/?)((?:'.$folders.')/[\w\-./]+?\.(?:png|jpe?g))(?=["\')])#i',
            function (array $match): string {
                $webp = self::pathFor($match[2]);

                return self::isAvailable($match[2], $webp) ? $match[1].$webp : $match[0];
            },
            $html
        ) ?? $html;
    }

    private static function isAvailable(string $original, string $webp): bool
    {
        if (! array_key_exists($webp, self::$available)) {
            $source = public_path($original);
            $target = public_path($webp);

            self::$available[$webp] = is_file($source)
                && is_file($target)
                && filemtime($target) >= filemtime($source);
        }

        return self::$available[$webp];
    }

    private static function orient(GdImage $image, string $file): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($file)['Orientation'] ?? 1);

        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        if (! $rotated instanceof GdImage) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }
}
