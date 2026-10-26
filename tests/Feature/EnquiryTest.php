<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_enquiry_is_stored_and_shown_to_superadmin(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('csrf-token', false)
            ->assertSee('Ready to get fit?');

        $this->postJson('/enquiries', [
            'name' => 'Asha Rao',
            'phone' => '9876543210',
            'email' => 'asha@example.test',
            'program' => 'Personal Training',
            'message' => 'Want to train three days a week.',
        ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Asha Rao',
            'email' => 'asha@example.test',
            'phone' => '9876543210',
            'program' => 'Personal Training',
        ]);

        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $enquiry = Enquiry::query()->firstOrFail();

        $this->actingAs($admin)
            ->get('/superadmin/dashboard')
            ->assertOk()
            ->assertSee('Asha Rao')
            ->assertSee('Personal Training')
            ->assertSee('Want to train three days a week.');

        $this->actingAs($admin)
            ->get('/superadmin/enquiries')
            ->assertOk()
            ->assertSee('Asha Rao')
            ->assertSee('asha@example.test')
            ->assertSee('New');

        $this->actingAs($admin)
            ->get('/superadmin/enquiries/'.$enquiry->id)
            ->assertOk()
            ->assertSee('Asha Rao')
            ->assertSee('9876543210')
            ->assertSee('Want to train three days a week.');

        $this->assertNotNull($enquiry->fresh()->read_at);
    }

    public function test_enquiry_requires_contact_fields(): void
    {
        $this->postJson('/enquiries', [
            'name' => '',
            'email' => 'not-an-email',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_customers_cannot_open_enquiries(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)->get('/superadmin/enquiries')->assertForbidden();
    }
}
