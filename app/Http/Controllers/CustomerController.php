<?php

namespace App\Http\Controllers;

use App\Models\CoachMessage;
use App\Models\NutritionPlan;
use App\Models\Payment;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard.customer', $this->portalPayload());
    }

    public function trainer(): View
    {
        return view('portal.customer.trainer', $this->portalPayload());
    }

    public function program(): View
    {
        return view('portal.customer.program', $this->portalPayload());
    }

    public function schedule(): View
    {
        return view('portal.customer.schedule', $this->portalPayload());
    }

    public function progress(): View
    {
        return view('portal.customer.progress', $this->portalPayload());
    }

    public function nutrition(): View
    {
        return view('portal.customer.nutrition', $this->portalPayload());
    }

    public function messages(): View
    {
        return view('portal.customer.messages', $this->portalPayload());
    }

    public function subscription(): View
    {
        return view('portal.customer.subscription', $this->portalPayload());
    }

    public function notifications(): View
    {
        return view('portal.customer.notifications', $this->portalPayload());
    }

    private function portalPayload(): array
    {
        $user = Auth::user()->loadMissing('trainer');
        $weekStart = now('Asia/Kolkata')->startOfWeek();
        $weekEnd = now('Asia/Kolkata')->endOfWeek();

        $plan = null;
        $planRow = null;
        $payment = null;

        if (Schema::hasTable('user_plans')) {
            $planRow = UserPlan::query()
                ->with(['payment', 'trainer'])
                ->where('user_id', $user->id)
                ->where('status', UserPlan::STATUS_ACTIVE)
                ->latest('starts_on')
                ->first();

            if ($planRow) {
                $plan = [
                    'name' => $planRow->name,
                    'total_weeks' => (int) $planRow->total_weeks,
                    'current_week' => $planRow->currentWeek(),
                    'status' => $planRow->status,
                    'amount_inr' => $planRow->amount_inr,
                    'labels' => $planRow->labels ?? [],
                    'trainer' => $planRow->trainer?->name ?: $user->trainer?->name,
                    'starts_on' => $planRow->starts_on?->format('d M Y'),
                ];
                $payment = $planRow->payment;
            }
        }

        if (! $payment && Schema::hasTable('payments')) {
            $payment = Payment::query()
                ->where('user_id', $user->id)
                ->where('status', Payment::STATUS_PAID)
                ->latest()
                ->first();
        }

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

        $streak = 0;
        if (Schema::hasTable('workout_logs')) {
            $dates = DB::table('workout_logs')->where('user_id', $user->id)
                ->distinct()->pluck('logged_on')
                ->map(fn ($d) => Carbon::parse($d)->toDateString());

            $cursor = $dates->contains(now('Asia/Kolkata')->toDateString())
                ? now('Asia/Kolkata')->toDateString()
                : now('Asia/Kolkata')->subDay()->toDateString();

            while ($dates->contains($cursor)) {
                $streak++;
                $cursor = Carbon::parse($cursor)->subDay()->toDateString();
            }
        }

        $hydrationMl = Schema::hasTable('hydration_logs')
            ? (int) DB::table('hydration_logs')->where('user_id', $user->id)
                ->whereDate('logged_on', today())->sum('ml')
            : 0;

        $badges = Schema::hasTable('user_badges')
            ? DB::table('user_badges')->where('user_id', $user->id)->count()
            : 0;

        $sessions = Schema::hasTable('scheduled_sessions')
            ? DB::table('scheduled_sessions')->where('user_id', $user->id)
                ->where('starts_at', '>=', now())->orderBy('starts_at')->limit(4)->get()
            : collect();

        $stats = [
            'streak' => $streak,
            'week_hours' => round(array_sum($weekActivity), 1),
            'week_activity' => array_map(fn ($h) => round($h, 2), $weekActivity),
            'hydration_l' => round($hydrationMl / 1000, 1),
            'hydration_goal_l' => 2,
            'badges' => $badges,
            'sessions_done' => $weekLogs->count(),
            'sessions_goal' => (int) ($planRow?->sessions_per_week ?? 0),
            'cardio_pct' => $kindPct('cardio', (int) ($planRow?->cardio_target ?? 0)),
            'strength_pct' => $kindPct('strength', (int) ($planRow?->strength_target ?? 0)),
        ];

        $coach = $user->trainer ?: $planRow?->trainer;
        $trainer = $coach?->name;
        $messages = Schema::hasTable('coach_messages')
            ? CoachMessage::query()
                ->where('trainee_id', $user->id)
                ->latest()
                ->limit(40)
                ->get()
                ->reverse()
                ->values()
            : collect();

        $nutritionPlans = Schema::hasTable('nutrition_plans')
            ? NutritionPlan::query()
                ->where('trainee_id', $user->id)
                ->latest()
                ->get()
            : collect();

        $coachInitials = collect(preg_split('/\s+/', trim($coach->name ?? '')))
            ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');

        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 1), '0'), '.');
        $clamp = fn ($n) => max(0, min(100, (int) round($n)));
        $byDay = array_values((array) data_get($stats, 'week_activity', []));
        $week = collect(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])
            ->map(fn ($d, $i) => ['d' => $d, 'h' => (float) ($byDay[$i] ?? 0)])->all();
        $maxH = max(1, collect($week)->max('h'));
        $todayIdx = now('Asia/Kolkata')->dayOfWeekIso - 1;
        $streak = (int) data_get($stats, 'streak', 0);
        $weekHours = (float) data_get($stats, 'week_hours', collect($week)->sum('h'));
        $hydration = (float) data_get($stats, 'hydration_l', 0);
        $hydrationGoal = (float) data_get($stats, 'hydration_goal_l', 2);
        $badges = (int) data_get($stats, 'badges', 0);
        $done = (int) data_get($stats, 'sessions_done', 0);
        $goal = max(0, (int) data_get($stats, 'sessions_goal', 0));
        $weekPct = $goal > 0 ? $clamp($done / $goal * 100) : 0;
        $ringOff = round(326.7 * (1 - $weekPct / 100), 1);
        $cardioPct = $clamp(data_get($stats, 'cardio_pct', 0));
        $strengthPct = $clamp(data_get($stats, 'strength_pct', 0));
        $planName = data_get($plan, 'name');
        $planWeek = (int) data_get($plan, 'current_week', 0);
        $planTotal = max(1, (int) data_get($plan, 'total_weeks', 12));
        $planStatus = (string) data_get($plan, 'status', 'active');
        $phaseNames = array_values((array) (data_get($plan, 'phases') ?: ['Foundation', 'Build', 'Strength & Conditioning', 'Peak']));
        $phaseLen = $planTotal / max(1, count($phaseNames));
        $phases = collect($phaseNames)->map(function ($name, $i) use ($phaseLen, $planWeek) {
            $start = (int) floor($i * $phaseLen) + 1;
            $end = (int) floor(($i + 1) * $phaseLen);
            $state = $planWeek > $end ? 'done' : ($planWeek >= $start ? 'now' : '');

            return compact('name', 'start', 'end', 'state');
        });
        $currentPhase = optional($phases->firstWhere('state', 'now'))['name'] ?? null;
        $iconFor = fn ($type) => match (strtolower((string) $type)) {
            'cardio', 'run', 'running' => 'i-run',
            'yoga', 'flexibility', 'mobility' => 'i-yoga',
            'hiit', 'conditioning' => 'i-zap',
            default => 'i-dumbbell',
        };
        $dayLabel = function ($when) {
            $c = Carbon::parse($when);

            return $c->isToday() ? 'Today' : ($c->isTomorrow() ? 'Tomorrow' : $c->format('D, d M'));
        };
        $nextToday = $sessions->first(fn ($s) => Carbon::parse(data_get($s, 'starts_at'))->isToday());
        $greet = \App\Support\DashboardGreeting::line($user->name);
        $initials = collect(preg_split('/\s+/', trim($user->name ?? 'TFI')))
            ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');

        return compact(
            'user', 'stats', 'plan', 'sessions', 'payment', 'trainer', 'coach', 'messages', 'nutritionPlans',
            'coachInitials', 'fmt', 'week', 'maxH', 'todayIdx', 'streak', 'weekHours', 'hydration', 'hydrationGoal',
            'badges', 'done', 'goal', 'weekPct', 'ringOff', 'cardioPct', 'strengthPct', 'planName', 'planWeek',
            'planTotal', 'planStatus', 'phases', 'currentPhase', 'iconFor', 'dayLabel', 'nextToday', 'greet', 'initials'
        );
    }

    public function storeMessage(Request $request): RedirectResponse
    {
        $user = Auth::user()->loadMissing('trainer');
        $coach = $user->trainer;

        if (! $coach) {
            return back()->withErrors(['body' => 'A trainer has not been assigned to you yet.']);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        CoachMessage::query()->create([
            'trainee_id' => $user->id,
            'trainer_id' => $coach->id,
            'body' => $data['body'],
            'from_role' => CoachMessage::FROM_TRAINEE,
        ]);

        return redirect()
            ->route('customer.messages.index')
            ->with('success', 'Message sent to '.$coach->name.'.');
    }
}
