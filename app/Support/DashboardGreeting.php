<?php

namespace App\Support;

class DashboardGreeting
{
    public const TIMEZONE = 'Asia/Kolkata';

    public static function line(?string $name = null): string
    {
        $hour = now(self::TIMEZONE)->hour;
        $greet = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        $name = trim((string) $name);

        return $name === '' ? $greet : $greet.' · '.$name;
    }

    public static function today(): string
    {
        return now(self::TIMEZONE)->format('l, d M Y');
    }
}
