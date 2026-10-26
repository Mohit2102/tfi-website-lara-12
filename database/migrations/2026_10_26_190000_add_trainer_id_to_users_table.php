<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('trainer_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
        });

        if (Schema::hasTable('user_plans')) {
            $rows = DB::table('user_plans')
                ->whereNotNull('trainer_id')
                ->where('status', 'active')
                ->orderByDesc('starts_on')
                ->get(['user_id', 'trainer_id']);

            foreach ($rows as $row) {
                DB::table('users')
                    ->where('id', $row->user_id)
                    ->whereNull('trainer_id')
                    ->update(['trainer_id' => $row->trainer_id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trainer_id');
        });
    }
};
