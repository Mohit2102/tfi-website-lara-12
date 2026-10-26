<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Facades\DB;

class PlanActivation
{
    public function __construct(private readonly PlanPricing $pricing) {}

    /**
     * @param  array<string, mixed>  $selection
     */
    public function quoteFor(array $selection): array
    {
        $quote = $this->pricing->priceFor($selection);
        $plan = is_string($selection['plan'] ?? null) && $selection['plan'] !== ''
            ? $selection['plan']
            : 'monthly-virtual';

        $sessions = $this->sessionsPerWeek($plan, $selection);
        $cardio = min(2, max(1, (int) floor($sessions / 2)));

        return array_merge($quote, [
            'plan_key' => $plan,
            'weeks' => $this->pricing->durationWeeks($plan, $selection),
            'sessions_per_week' => $sessions,
            'cardio_target' => $cardio,
            'strength_target' => max(0, $sessions - $cardio),
        ]);
    }

    /**
     * @param  array<string, mixed>  $selection
     */
    public function start(User $user, array $selection, string $source = Payment::SOURCE_RAZORPAY): Payment
    {
        $quote = $this->quoteFor($selection);

        return Payment::query()->create([
            'user_id' => $user->id,
            'status' => Payment::STATUS_PENDING,
            'source' => $source,
            'amount_inr' => $quote['rupees'],
            'amount_usd' => $quote['usd'],
            'currency' => 'INR',
            'plan_key' => $quote['plan_key'],
            'plan_name' => $quote['name'],
            'selection' => $selection,
            'labels' => $quote['labels'],
        ]);
    }

    public function complete(Payment $payment, ?string $paymentId = null): UserPlan
    {
        return DB::transaction(function () use ($payment, $paymentId) {
            $payment->refresh();

            if ($payment->isPaid() && $payment->plan) {
                return $payment->plan;
            }

            $quote = $this->quoteFor((array) $payment->selection);
            $trainee = User::query()->find($payment->user_id);
            $trainerId = $trainee?->trainer_id;

            UserPlan::query()
                ->where('user_id', $payment->user_id)
                ->where('status', UserPlan::STATUS_ACTIVE)
                ->update(['status' => UserPlan::STATUS_PAUSED]);

            $plan = UserPlan::query()->create([
                'user_id' => $payment->user_id,
                'trainer_id' => $trainerId,
                'name' => $payment->plan_name,
                'plan_key' => $payment->plan_key,
                'amount_inr' => $payment->amount_inr,
                'labels' => $payment->labels,
                'payment_id' => $payment->id,
                'total_weeks' => $quote['weeks'],
                'starts_on' => now()->toDateString(),
                'status' => UserPlan::STATUS_ACTIVE,
                'sessions_per_week' => $quote['sessions_per_week'],
                'cardio_target' => $quote['cardio_target'],
                'strength_target' => $quote['strength_target'],
            ]);

            $payment->forceFill([
                'status' => Payment::STATUS_PAID,
                'payment_id' => $paymentId ?: $payment->payment_id ?: $payment->source.'_'.$payment->id,
            ])->save();

            return $plan;
        });
    }

    /**
     * @param  array<string, mixed>  $selection
     */
    public function dummyPurchase(User $user, array $selection): Payment
    {
        $payment = $this->start($user, $selection, Payment::SOURCE_DUMMY);
        $this->complete($payment, 'dummy_'.$payment->id);

        return $payment->fresh();
    }

    /**
     * @param  array<string, mixed>  $selection
     */
    private function sessionsPerWeek(string $plan, array $selection): int
    {
        $session = $selection['session'] ?? null;

        if (in_array($plan, ['monthly-virtual', ''], true) && is_string($session)) {
            return max(3, (int) round(((int) $session) / 4));
        }

        if (is_string($session) && in_array($session, ['3', '4', '5'], true)) {
            return (int) $session;
        }

        return 3;
    }
}
