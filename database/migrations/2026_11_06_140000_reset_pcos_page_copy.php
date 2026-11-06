<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old copy saved from the
     * dashboard would keep overriding the rewritten PMOS / PCOS / PCOD page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'pcos-pcod-nutrition')->first();

        if (! $page) {
            return;
        }

        $content = json_decode((string) $page->content, true);
        $image = is_array($content) ? (string) ($content['hero']['image'] ?? '') : '';

        $updates = [
            'content' => $image !== '' ? json_encode(['hero' => ['image' => $image]]) : null,
            'html' => null,
            'updated_at' => now(),
        ];

        if ($page->title === 'PCOS/PCOD Balanced Nutrition | TFI — Time To Fit India') {
            $updates['title'] = 'PMOS / PCOS / PCOD | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Strength training, balanced nutrition and routine support for women managing PCOS')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Structured strength training, daily movement and nutrition guidance for women living with PMOS / PCOS / PCOD.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
