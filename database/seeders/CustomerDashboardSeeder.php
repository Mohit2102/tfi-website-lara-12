<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fills the customer dashboard tables with realistic dummy data.
 *
 *   php artisan db:seed --class=CustomerDashboardSeeder
 *
 * Safe to re-run: it first clears these five tables for the same users, then
 * inserts fresh data relative to today's date, so streaks and "upcoming"
 * sessions are always current. It never touches the users table.
 */
class CustomerDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Seed every customer; if none are found, seed the first user so you still see something.
        $userIds = Schema::hasColumn('users', 'role')
            ? DB::table('users')->where('role', 'customer')->pluck('id')
            : collect();

        if ($userIds->isEmpty()) {
            $userIds = DB::table('users')->orderBy('id')->limit(1)->pluck('id');
        }

        if ($userIds->isEmpty()) {
            $this->command?->warn('No users found. Register a user first, then run this seeder.');
            return;
        }

        // Start clean for these users (re-runnable)
        foreach (['scheduled_sessions', 'user_badges', 'hydration_logs', 'workout_logs', 'user_plans'] as $table) {
            DB::table($table)->whereIn('user_id', $userIds)->delete();
        }

        foreach ($userIds as $id) {
            $this->seedUser((int) $id);
        }

        $this->command?->info('Dummy dashboard data created for ' . $userIds->count() . ' user(s): ' . $userIds->implode(', '));
    }

    private function seedUser(int $userId): void
    {
        $now   = now();
        $stamp = ['created_at' => $now, 'updated_at' => $now];

        // ── Active plan: currently in week 7 of 12 ─────────────────────
        DB::table('user_plans')->insert([
            'user_id'           => $userId,
            'name'              => '12-Week Virtual Training Program',
            'total_weeks'       => 12,
            'starts_on'         => today()->subWeeks(6)->toDateString(),
            'status'            => 'active',
            'sessions_per_week' => 5,
            'cardio_target'     => 2,
            'strength_target'   => 3,
        ] + $stamp);

        // ── Workouts: one per day for the last 12 days (a 12-day streak) ─
        $kinds = [
            ['cardio',   'Morning Cardio Run',  45],
            ['strength', 'Upper Body Strength', 60],
            ['strength', 'Lower Body Strength', 55],
            ['yoga',     'Yoga & Flexibility',  30],
            ['cardio',   'Interval Cycling',    40],
            ['strength', 'Full Body Strength',  50],
            ['hiit',     'Full Body HIIT',      35],
        ];

        $logs = [];
        for ($d = 0; $d < 12; $d++) {
            [$kind, $title, $minutes] = $kinds[$d % count($kinds)];
            $logs[] = [
                'user_id'   => $userId,
                'logged_on' => today()->subDays($d)->toDateString(),
                'minutes'   => $minutes,
                'kind'      => $kind,
                'title'     => $title,
            ] + $stamp;
        }
        DB::table('workout_logs')->insert($logs);

        // ── Hydration: 2.1 L today (goal met), plus the past week ──────
        $water = [
            [today(), 800], [today(), 700], [today(), 600],
        ];
        foreach (range(1, 6) as $d) {
            $water[] = [today()->subDays($d), 900];
            $water[] = [today()->subDays($d), 1100];
        }
        DB::table('hydration_logs')->insert(array_map(fn ($w) => [
            'user_id'   => $userId,
            'logged_on' => $w[0]->toDateString(),
            'ml'        => $w[1],
        ] + $stamp, $water));

        // ── Badges earned ──────────────────────────────────────────────
        $badges = [
            ['First Workout', 40], ['7-Day Streak', 20], ['Hydration Hero', 14],
            ['Early Bird', 9],     ['Strength Starter', 3],
        ];
        DB::table('user_badges')->insert(array_map(fn ($b) => [
            'user_id'   => $userId,
            'badge'     => $b[0],
            'earned_at' => now()->subDays($b[1]),
        ] + $stamp, $badges));

        // ── Upcoming sessions (always in the future) ───────────────────
        $sessions = [
            ['Evening Cardio Run',  'cardio',   now()->addHours(2),                        45],
            ['Upper Body Strength', 'strength', now()->addDay()->setTime(19, 0),           60],
            ['Yoga & Flexibility',  'yoga',     now()->addDays(2)->setTime(8, 0),          30],
            ['Full Body HIIT',      'hiit',     now()->addDays(3)->setTime(18, 30),        50],
        ];
        DB::table('scheduled_sessions')->insert(array_map(fn ($s) => [
            'user_id'      => $userId,
            'title'        => $s[0],
            'type'         => $s[1],
            'starts_at'    => $s[2],
            'duration_min' => $s[3],
        ] + $stamp, $sessions));
    }
}
