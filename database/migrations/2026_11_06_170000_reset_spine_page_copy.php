<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old copy saved from the
     * dashboard would keep overriding the rewritten spine page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'back-spine-posture')->first();

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

        if ($page->title === 'Lower Back and Spine Pain / Posture Correction | TFI — Time To Fit India') {
            $updates['title'] = 'Cervical & Lumbar Spine Conditions | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Mobility, core stability and posture-focused training')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Personalised exercise and nutrition guidance for individuals with cervical or lumbar spine conditions, adapted to their symptoms and medical recommendations.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
