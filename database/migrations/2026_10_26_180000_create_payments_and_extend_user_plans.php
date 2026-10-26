<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('source')->default('razorpay');
            $table->string('order_id')->nullable()->unique();
            $table->string('payment_id')->nullable()->unique();
            $table->unsignedInteger('amount_inr')->default(0);
            $table->decimal('amount_usd', 10, 2)->nullable();
            $table->string('currency', 8)->default('INR');
            $table->string('plan_key');
            $table->string('plan_name');
            $table->json('selection')->nullable();
            $table->json('labels')->nullable();
            $table->timestamps();
        });

        Schema::table('user_plans', function (Blueprint $table) {
            $table->string('plan_key')->nullable()->after('name');
            $table->unsignedInteger('amount_inr')->nullable()->after('plan_key');
            $table->json('labels')->nullable()->after('amount_inr');
            $table->foreignId('payment_id')->nullable()->after('labels')->constrained('payments')->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
            $table->dropConstrainedForeignId('trainer_id');
            $table->dropColumn(['plan_key', 'amount_inr', 'labels']);
        });

        Schema::dropIfExists('payments');
    }
};
