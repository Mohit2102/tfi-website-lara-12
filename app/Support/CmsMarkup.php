<?php

namespace App\Support;

use Illuminate\Support\Arr;

class CmsMarkup
{
    /**
     * @param  array<string, mixed>  $content
     */
    public static function apply(string $html, array $content): string
    {
        foreach (Arr::dot($content) as $key => $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $html = self::replaceMarked($html, $key, (string) $value);
        }

        return $html;
    }

    /**
     * Replace marked copy only when it differs from the original defaults,
     * so unedited markup (and its CSS) stays untouched.
     *
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $content
     */
    public static function applyChanged(string $html, array $defaults, array $content): string
    {
        $original = Arr::dot($defaults);

        foreach (Arr::dot($content) as $key => $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $baseline = $original[$key] ?? null;

            if (is_scalar($baseline) && (string) $baseline === (string) $value) {
                continue;
            }

            $html = self::replaceMarked($html, $key, (string) $value);
        }

        return $html;
    }

    private static function replaceMarked(string $html, string $key, string $value): string
    {
        $quoted = preg_quote($key, '/');
        $safe = nl2br(e($value), false);
        $attr = e($value);

        $html = preg_replace_callback(
            '/(data-cms="'.$quoted.'"[^>]*>)(.*?)(<\/)/s',
            fn (array $match): string => $match[1].$safe.$match[3],
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/(data-cms-prefix="'.$quoted.'"[^>]*>)(\s*)([^<]*)/',
            fn (array $match): string => $match[1].$match[2].$safe,
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/(data-cms-tail="'.$quoted.'"[^>]*>)(.*)(<\/)/s',
            function (array $match) use ($safe): string {
                $inner = preg_replace(
                    '/^(.*<[^>]+>)(\s*)([^<]*)$/s',
                    '${1}${2}'.$safe,
                    $match[2]
                ) ?? $match[2];

                return $match[1].$inner.$match[3];
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/src="[^"]*"([^>]*\s)?data-cms-src="'.$quoted.'"/',
            fn (array $match): string => 'src="'.$attr.'"'.($match[1] ?? '').'data-cms-src="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-cms-src="'.$quoted.'"([^>]*)\ssrc="[^"]*"/',
            fn (array $match): string => 'data-cms-src="'.$key.'"'.$match[1].' src="'.$attr.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/alt="[^"]*"([^>]*\s)?data-cms-alt="'.$quoted.'"/',
            fn (array $match): string => 'alt="'.$attr.'"'.($match[1] ?? '').'data-cms-alt="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/style="background-image:url\(\'[^\']*\'\)"([^>]*\s)?data-cms-bg="'.$quoted.'"/',
            fn (array $match): string => 'style="background-image:url(\''.$attr.'\')"'.($match[1] ?? ' ').'data-cms-bg="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-cms-bg="'.$quoted.'"([^>]*)style="background-image:url\(\'[^\']*\'\)"/',
            fn (array $match): string => 'data-cms-bg="'.$key.'"'.$match[1].'style="background-image:url(\''.$attr.'\')"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-target="[^"]*"([^>]*\s)?data-cms-target="'.$quoted.'"/',
            fn (array $match): string => 'data-target="'.$attr.'"'.($match[1] ?? '').'data-cms-target="'.$key.'"',
            $html
        ) ?? $html;

        return preg_replace_callback(
            '/data-cms-target="'.$quoted.'"([^>]*)data-target="[^"]*"/',
            fn (array $match): string => 'data-cms-target="'.$key.'"'.$match[1].'data-target="'.$attr.'"',
            $html
        ) ?? $html;
    }
}
