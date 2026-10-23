<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The plan a customer has bought / been assigned
        Schema::create('user_plans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');                              // e.g. 12-Week Virtual Training Program
            $t->unsignedSmallInteger('total_weeks')->default(12);
            $t->date('starts_on');
            $t->string('status')->default('active');         // active | paused | completed
            $t->unsignedTinyInteger('sessions_per_week')->default(0);
            $t->unsignedTinyInteger('cardio_target')->default(0);    // cardio sessions / week
            $t->unsignedTinyInteger('strength_target')->default(0);  // strength sessions / week
            $t->timestamps();
        });

        // One row per completed workout
        Schema::create('workout_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('logged_on');
            $t->unsignedSmallInteger('minutes');
            $t->string('kind')->default('strength');         // cardio | strength | yoga | hiit
            $t->string('title')->nullable();
            $t->timestamps();
            $t->index(['user_id', 'logged_on']);
        });

        Schema::create('hydration_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->date('logged_on');
            $t->unsignedSmallInteger('ml');
            $t->timestamps();
            $t->index(['user_id', 'logged_on']);
        });

        Schema::create('user_badges', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('badge');
            $t->timestamp('earned_at')->useCurrent();
            $t->timestamps();
        });

        // Sessions your coach schedules for the customer
        Schema::create('scheduled_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('type')->default('strength');         // cardio | strength | yoga | hiit
            $t->dateTime('starts_at');
            $t->unsignedSmallInteger('duration_min')->default(45);
            $t->timestamps();
            $t->index(['user_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_sessions');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('hydration_logs');
        Schema::dropIfExists('workout_logs');
        Schema::dropIfExists('user_plans');
    }
};
