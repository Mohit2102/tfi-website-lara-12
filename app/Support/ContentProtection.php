<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class ContentProtection
{
    public static function enabled(): bool
    {
        if (! config('app.content_protection')) {
            return false;
        }

        return ! Auth::user()?->isSuperAdmin();
    }

    public static function apply(string $html): string
    {
        $html = preg_replace(
            '/<head[^>]*>/i',
            '$0<script src="/tfi-protect.js"></script>',
            $html,
            1
        ) ?? $html;

        return self::minify($html);
    }

    public static function minify(string $html): string
    {
        $preserved = [];

        // Whitespace inside these blocks is significant (inline JS, preformatted text, the calculator formulas).
        $html = preg_replace_callback(
            '/<(script|pre|textarea)\b[^>]*>.*?<\/\1>|<div\b[^>]*\bclass="eq"[^>]*>.*?<\/div>/is',
            function (array $match) use (&$preserved): string {
                $key = "\x00".count($preserved)."\x00";
                $preserved[$key] = $match[0];

                return $key;
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/(<style\b[^>]*>)(.*?)(<\/style>)/is',
            fn (array $match): string => $match[1].self::minifyCss($match[2]).$match[3],
            $html
        ) ?? $html;

        $html = preg_replace('/<!--(?!\[if).*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/\s+/', ' ', $html) ?? $html;

        return trim(strtr($html, $preserved));
    }

    private static function minifyCss(string $css): string
    {
        $css = preg_replace('#/\*.*?\*/#s', '', $css) ?? $css;
        $css = preg_replace('/\s+/', ' ', $css) ?? $css;
        $css = preg_replace('/\s*([{};,>])\s*/', '$1', $css) ?? $css;

        return trim(str_replace(';}', '}', $css));
    }
}
