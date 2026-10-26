<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use App\Models\UserPlan;
use App\Services\PlanActivation;
use Illuminate\Database\Seeder;

class DemoPlanPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $activation = app(PlanActivation::class);

        User::query()->where('role', User::ROLE_CUSTOMER)->each(function (User $user) use ($activation) {
            $plan = $user->plans()->where('status', UserPlan::STATUS_ACTIVE)->latest('starts_on')->first();

            if (! $plan) {
                $activation->dummyPurchase($user, ['plan' => 'monthly-nv']);

                return;
            }

            if ($plan->payment_id) {
                return;
            }

            $payment = Payment::query()->create([
                'user_id' => $user->id,
                'status' => Payment::STATUS_PAID,
                'source' => Payment::SOURCE_DUMMY,
                'amount_inr' => $plan->amount_inr ?: 5999,
                'currency' => 'INR',
                'plan_key' => $plan->plan_key ?: 'monthly-nv',
                'plan_name' => $plan->name,
                'selection' => ['plan' => $plan->plan_key ?: 'monthly-nv'],
                'labels' => $plan->labels,
                'payment_id' => 'dummy_seed_'.$plan->id,
            ]);

            $plan->forceFill([
                'payment_id' => $payment->id,
                'plan_key' => $plan->plan_key ?: 'monthly-nv',
                'amount_inr' => $plan->amount_inr ?: 5999,
                'trainer_id' => $plan->trainer_id ?: $user->trainer_id,
            ])->save();
        });
    }
}
