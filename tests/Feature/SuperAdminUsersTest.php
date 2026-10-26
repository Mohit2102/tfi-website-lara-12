<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_list_users_by_role(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPERADMIN, 'name' => 'Boss']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Staff Admin']);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'name' => 'Pat Customer']);

        $this->actingAs($super)->get('/superadmin/users')
            ->assertOk()
            ->assertSee('Boss')
            ->assertSee('Staff Admin')
            ->assertSee('Pat Customer');

        $this->actingAs($super)->get('/superadmin/users?role=admin')
            ->assertOk()
            ->assertSee('Staff Admin')
            ->assertDontSee('Pat Customer');

        $this->actingAs($super)->get('/superadmin/users?role=customer')
            ->assertOk()
            ->assertSee('Pat Customer')
            ->assertDontSee('Staff Admin');
    }

    public function test_superadmin_can_create_and_update_a_customer(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($super)
            ->post('/superadmin/users', [
                'name' => 'New Customer',
                'email' => 'new-customer@example.test',
                'phone' => '9000000000',
                'role' => User::ROLE_CUSTOMER,
                'password' => 'secret-pass',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $customer = User::query()->where('email', 'new-customer@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $customer->role);

        $this->actingAs($super)
            ->put('/superadmin/users/'.$customer->id, [
                'name' => 'Updated Customer',
                'email' => $customer->email,
                'phone' => '9000000001',
                'role' => User::ROLE_CUSTOMER,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('Updated Customer', $customer->fresh()->name);
    }

    public function test_superadmin_cannot_delete_themselves(): void
    {
        $super = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($super)
            ->delete('/superadmin/users/'.$super->id)
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $super->id]);
    }

    public function test_customers_cannot_manage_users(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)->get('/superadmin/users')->assertForbidden();
    }
}
