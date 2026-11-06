<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saving a program page stores every field, so old copy saved from the
     * dashboard would keep overriding the rewritten postnatal page.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'prenatal-postnatal-fitness')->first();

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

        if ($page->title === 'Pre and Post Natal Fitness Safe Workouts | TFI — Time To Fit India') {
            $updates['title'] = 'Post-Pregnancy Fitness & Recovery | TFI — Time To Fit India';
        }

        if (str_starts_with((string) $page->meta_description, 'TFI — Time To Fit India. Gentle, safe and effective training for pregnancy')) {
            $updates['meta_description'] = 'TFI — Time To Fit India. Personalised exercise and nutrition guidance to help mothers rebuild strength, improve fitness and develop healthy habits after childbirth.';
        }

        DB::table('site_pages')->where('id', $page->id)->update($updates);
    }

    public function down(): void
    {
        //
    }
};
