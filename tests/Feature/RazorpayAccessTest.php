<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_or_verify_payment_orders(): void
    {
        $this->postJson('/api/razorpay/create-order', [])
            ->assertUnauthorized()
            ->assertJsonPath('error', 'Please log in as a customer to purchase a plan.');

        $this->postJson('/api/razorpay/verify', [])
            ->assertUnauthorized()
            ->assertJsonPath('error', 'Please log in as a customer to purchase a plan.');
    }

    public function test_only_customers_can_create_or_verify_payment_orders(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->postJson('/api/razorpay/create-order', [])
            ->assertForbidden()
            ->assertJsonPath('error', 'Only customer accounts can purchase a plan.');

        $this->actingAs($admin)
            ->postJson('/api/razorpay/verify', [])
            ->assertForbidden()
            ->assertJsonPath('error', 'Only customer accounts can purchase a plan.');
    }

    public function test_customer_order_pricing_is_validated_by_the_server(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->postJson('/api/razorpay/create-order', [])
            ->assertBadRequest()
            ->assertJsonPath('error', 'Invalid plan selection.');
    }
}
