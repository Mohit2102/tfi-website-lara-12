<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Saved card text is keyed by position, and the program cards were reordered,
     * so stale values would land on the wrong cards.
     */
    public function up(): void
    {
        $page = DB::table('site_pages')->where('slug', 'personal-training')->first();

        if (! $page || blank($page->content)) {
            return;
        }

        $content = json_decode($page->content, true);

        if (! is_array($content)) {
            return;
        }

        unset($content['intro'], $content['cards']);

        DB::table('site_pages')->where('id', $page->id)->update([
            'content' => $content === [] ? null : json_encode($content),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
