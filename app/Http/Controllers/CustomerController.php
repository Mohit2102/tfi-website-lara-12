<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Feeds resources/views/dashboard/customer.blade.php with real data.
     * Every query is guarded with Schema::hasTable(), so the page still renders
     * (with empty states) before the dashboard migration has been run.
     */
    public function dashboard(): View
    {
        $user      = Auth::user();
        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        // ── Active plan ────────────────────────────────────────────────
        $plan    = null;
        $planRow = null;
        if (Schema::hasTable('user_plans')) {
            $planRow = DB::table('user_plans')
                ->where('user_id', $user->id)->where('status', 'active')
                ->orderByDesc('starts_on')->first();

            if ($planRow) {
                $weeksIn = (int) floor(Carbon::parse($planRow->starts_on)->diffInDays(now(), false) / 7) + 1;
                $plan = [
                    'name'         => $planRow->name,
                    'total_weeks'  => (int) $planRow->total_weeks,
                    'current_week' => max(1, min((int) $planRow->total_weeks, $weeksIn)),
                    'status'       => $planRow->status,
                ];
            }
        }

        // ── Workouts this week ─────────────────────────────────────────
        $weekLogs = collect();
        if (Schema::hasTable('workout_logs')) {
            $weekLogs = DB::table('workout_logs')
                ->where('user_id', $user->id)
                ->whereBetween('logged_on', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->get();
        }

        $weekActivity = array_fill(0, 7, 0);
        foreach ($weekLogs as $log) {
            $weekActivity[Carbon::parse($log->logged_on)->dayOfWeekIso - 1] += $log->minutes / 60;
        }

        $kindPct = fn (string $kind, int $target) => $target > 0
            ? min(100, round($weekLogs->where('kind', $kind)->count() / $target * 100))
            : 0;

        // ── Streak (consecutive days with a logged workout) ────────────
        $streak = 0;
        if (Schema::hasTable('workout_logs')) {
            $dates = DB::table('workout_logs')->where('user_id', $user->id)
                ->distinct()->pluck('logged_on')
                ->map(fn ($d) => Carbon::parse($d)->toDateString());

            $cursor = $dates->contains(now()->toDateString())
                ? now()->toDateString()
                : now()->subDay()->toDateString();

            while ($dates->contains($cursor)) {
                $streak++;
                $cursor = Carbon::parse($cursor)->subDay()->toDateString();
            }
        }

        // ── Hydration + badges ─────────────────────────────────────────
        $hydrationMl = Schema::hasTable('hydration_logs')
            ? (int) DB::table('hydration_logs')->where('user_id', $user->id)
                ->whereDate('logged_on', today())->sum('ml')
            : 0;

        $badges = Schema::hasTable('user_badges')
            ? DB::table('user_badges')->where('user_id', $user->id)->count()
            : 0;

        // ── Upcoming sessions ──────────────────────────────────────────
        $sessions = Schema::hasTable('scheduled_sessions')
            ? DB::table('scheduled_sessions')->where('user_id', $user->id)
                ->where('starts_at', '>=', now())->orderBy('starts_at')->limit(4)->get()
            : collect();

        $stats = [
            'streak'           => $streak,
            'week_hours'       => round(array_sum($weekActivity), 1),
            'week_activity'    => array_map(fn ($h) => round($h, 2), $weekActivity),
            'hydration_l'      => round($hydrationMl / 1000, 1),
            'hydration_goal_l' => 2,
            'badges'           => $badges,
            'sessions_done'    => $weekLogs->count(),
            'sessions_goal'    => (int) ($planRow->sessions_per_week ?? 0),
            'cardio_pct'       => $kindPct('cardio', (int) ($planRow->cardio_target ?? 0)),
            'strength_pct'     => $kindPct('strength', (int) ($planRow->strength_target ?? 0)),
        ];

        return view('dashboard.customer', compact('user', 'stats', 'plan', 'sessions'));
    }
}
