<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old copy saved from the
     * dashboard would keep overriding the rewritten thyroid page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'thyroid-metabolic-support')->first();

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

        if ($page->title === 'Thyroid Management / Metabolic Support | TFI — Time To Fit India') {
            $updates['title'] = 'Hypothyroidism & Hyperthyroidism | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Consistent training, balanced nutrition')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Structured strength training, energy-based movement and nutrition guidance for people living with hypothyroidism or hyperthyroidism.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
