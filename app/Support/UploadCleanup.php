<?php

namespace App\Support;

use Illuminate\Support\Arr;

class UploadCleanup
{
    /**
     * Image fields cleared in the dashboard arrive as null (empty-string middleware);
     * store them as "" so the page renderer blanks the image instead of skipping it.
     *
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    public static function blankClearedImages(array $content): array
    {
        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $content[$key] = self::blankClearedImages($value);
            } elseif ($value === null && is_string($key) && ($key === 'image' || str_ends_with($key, '_image'))) {
                $content[$key] = '';
            }
        }

        return $content;
    }

    /**
     * Delete uploaded files under $prefix that the old content used and the new content no longer does.
     *
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>  $new
     */
    public static function orphans(?array $old, array $new, string $prefix): void
    {
        $kept = array_filter(Arr::dot($new), 'is_string');

        foreach (Arr::dot($old ?? []) as $path) {
            if (! is_string($path) || ! str_starts_with($path, $prefix) || in_array($path, $kept, true)) {
                continue;
            }

            $file = public_path($path);

            foreach ([$file, WebpImage::pathFor($file)] as $target) {
                if (is_file($target)) {
                    @unlink($target);
                }
            }
        }
    }
}
