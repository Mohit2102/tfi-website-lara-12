<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['trainer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_messages');
    }
};
