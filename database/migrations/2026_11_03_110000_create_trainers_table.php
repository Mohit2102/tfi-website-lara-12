<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('specialization', 120)->nullable();
            $table->string('experience', 40)->nullable();
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->json('tags')->nullable();
            $table->string('certification', 160)->nullable();
            $table->string('highlight', 160)->nullable();
            $table->string('languages', 160)->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['Arjun Mehta', 'Strength & Conditioning', '8+ Years', 'Builds raw strength and athletic power with structured, progressive programming.', ['Strength Training', 'Powerlifting', 'Athletic Performance'], 'NSCA-CSCS Certified', '300+ clients trained', 'English, Hindi'],
            ['Priya Sharma', 'Fat Loss & Nutrition', '6+ Years', 'Helps clients lose fat sustainably with smart training and practical nutrition habits.', ['Weight Loss', 'Diet Planning', 'Body Recomposition'], 'ACE CPT & Nutrition Coach', '250+ transformations', 'English, Hindi, Kannada'],
            ['Kavya Nair', 'Yoga & Mobility', '10+ Years', 'Improves flexibility, posture and mental calm through mindful movement.', ['Yoga', 'Mobility', 'Stress Relief'], 'RYT-500 Certified Yoga Teacher', '500+ sessions led', 'English, Malayalam'],
            ['Rohan Kapoor', 'Bodybuilding & Physique', '9+ Years', 'Sculpts muscle and physique with hypertrophy-focused plans and contest-style prep.', ['Muscle Gain', 'Hypertrophy', 'Contest Prep'], 'ISSA Certified Trainer', 'State-level competitor', 'English, Hindi'],
            ['Sneha Reddy', 'HIIT & Functional Fitness', '5+ Years', 'High-energy sessions that boost endurance, burn calories and keep workouts fun.', ['HIIT', 'Functional Training', 'Endurance'], 'CrossFit Level 2 Trainer', '200+ group classes', 'English, Telugu, Hindi'],
            ['Vikram Iyer', 'Rehab & Senior Fitness', '12+ Years', 'Safe, gentle coaching for injury recovery, back pain and active ageing.', ['Injury Recovery', 'Posture Correction', 'Senior Fitness'], 'BPT & Corrective Exercise Specialist', '400+ rehab clients', 'English, Tamil, Hindi'],
        ];

        DB::table('trainers')->insert(array_map(
            fn (array $row, int $i): array => [
                'name' => $row[0],
                'specialization' => $row[1],
                'experience' => $row[2],
                'bio' => $row[3],
                'tags' => json_encode($row[4]),
                'certification' => $row[5],
                'highlight' => $row[6],
                'languages' => $row[7],
                'sort_order' => $i + 1,
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $defaults,
            array_keys($defaults)
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
