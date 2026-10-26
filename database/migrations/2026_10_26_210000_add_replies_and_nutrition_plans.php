<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coach_messages', function (Blueprint $table) {
            $table->string('from_role')->default('trainee')->after('body');
        });

        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_plans');

        Schema::table('coach_messages', function (Blueprint $table) {
            $table->dropColumn('from_role');
        });
    }
};
