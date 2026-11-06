<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const RENAMES = [
        'cardiovascular-conditioning' => 'arthritis-joint-mobility',
        'gerd-support' => 'age-related-strength-balance-mobility',
    ];

    public function up(): void
    {
        foreach (self::RENAMES as $from => $to) {
            $this->rename($from, $to);
        }
    }

    public function down(): void
    {
        foreach (self::RENAMES as $from => $to) {
            $this->rename($to, $from);
        }
    }

    private function rename(string $from, string $to): void
    {
        if (DB::table('site_pages')->where('slug', $to)->exists()) {
            return;
        }

        DB::table('site_pages')->where('slug', $from)->update(['slug' => $to]);
    }
};
