<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old GERD copy saved from
     * the dashboard would keep overriding the rewritten senior fitness page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'age-related-strength-balance-mobility')->first();

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

        if ($page->title === 'GERD (Gastroesophageal Reflux Disease) | TFI — Time To Fit India') {
            $updates['title'] = 'Age-Related Strength, Balance & Mobility | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Reflux-aware training')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Personalised exercise and nutrition guidance to help older adults maintain strength, improve balance and mobility, and stay independent in everyday activities.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
