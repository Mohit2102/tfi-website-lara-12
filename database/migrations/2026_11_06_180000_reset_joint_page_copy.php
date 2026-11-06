<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old cardio copy saved from
     * the dashboard would keep overriding the rewritten joint mobility page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'arthritis-joint-mobility')->first();

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

        if ($page->title === 'Cardiovascular Health / Cardio Conditioning | TFI — Time To Fit India') {
            $updates['title'] = 'Arthritis & Joint Mobility | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Progressive, well-paced cardio programming')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Personalised exercise and nutrition guidance for people living with arthritis, to build strength, maintain joint mobility and improve everyday function.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
