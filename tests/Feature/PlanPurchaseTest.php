<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanPurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.razorpay.key' => '',
            'services.razorpay.secret' => '',
        ]);
    }

    public function test_customer_dummy_checkout_assigns_the_selected_plan(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'name' => 'Riya Trainee']);

        $this->actingAs($customer)
            ->postJson('/api/razorpay/create-order', ['plan' => 'monthly-nv'])
            ->assertOk()
            ->assertJsonPath('dummy', true)
            ->assertJsonPath('plan', 'Monthly Non-Virtual Training');

        $this->assertDatabaseHas('user_plans', [
            'user_id' => $customer->id,
            'trainer_id' => null,
            'plan_key' => 'monthly-nv',
            'status' => UserPlan::STATUS_ACTIVE,
        ]);

        $this->actingAs($customer)
            ->get('/customer/program')
            ->assertOk()
            ->assertSee('Monthly Non-Virtual Training');
    }

    public function test_superadmin_assignment_shows_on_trainer_and_trainee_dashboards(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $trainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Anya']);
        $otherTrainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Ben']);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'name' => 'Riya Trainee']);

        $this->actingAs($customer)
            ->postJson('/api/razorpay/create-order', ['plan' => 'monthly-nv'])
            ->assertOk();

        $this->actingAs($super)
            ->get('/superadmin/assignments')
            ->assertOk()
            ->assertSee('Riya Trainee')
            ->assertSee('Coach Anya');

        $this->actingAs($super)
            ->put('/superadmin/assignments', [
                'trainer_id' => [$customer->id => $trainer->id],
            ])
            ->assertRedirect('/superadmin/assignments');

        $this->assertSame($trainer->id, $customer->fresh()->trainer_id);
        $this->assertSame($trainer->id, UserPlan::query()->where('user_id', $customer->id)->value('trainer_id'));

        $this->actingAs($customer)
            ->get('/customer/trainer')
            ->assertOk()
            ->assertSee('Your trainer')
            ->assertSee('Coach Anya')
            ->assertSee($trainer->email);

        $this->actingAs($customer)
            ->get('/customer/program')
            ->assertOk()
            ->assertSee('Monthly Non-Virtual Training');

        $this->actingAs($trainer)
            ->get('/admin/trainees')
            ->assertOk()
            ->assertSee('Riya Trainee')
            ->assertSee('Monthly Non-Virtual Training');

        $this->actingAs($otherTrainer)
            ->get('/admin/trainees')
            ->assertOk()
            ->assertDontSee('Riya Trainee');
    }

    public function test_invalid_plan_is_rejected_before_dummy_checkout(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->postJson('/api/razorpay/create-order', [])
            ->assertBadRequest()
            ->assertJsonPath('error', 'Invalid plan selection.');

        $this->assertDatabaseCount('payments', 0);
    }
}
