<?php

namespace Tests\Feature;

use App\Models\PlanPrice;
use App\Models\User;
use App\Services\PlanPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SuperAdminCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_update_a_public_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->put('/superadmin/pages/about', [
                'title' => 'About TFI edited',
                'meta_description' => 'Edited description',
                'html' => '<html><head><title>Old</title><meta name="description" content="old"></head><body><h1>Edited about page</h1></body></html>',
                'is_published' => '1',
            ])
            ->assertRedirect('/superadmin/pages/about/edit');

        $this->get('/about')
            ->assertOk()
            ->assertSee('Edited about page', false)
            ->assertSee('<title>About TFI edited</title>', false);
    }

    public function test_hidden_pages_return_not_found(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->put('/superadmin/pages/about', [
                'title' => 'Hidden',
                'html' => '<html><body>secret</body></html>',
            ])
            ->assertRedirect();

        $this->get('/about')->assertNotFound();
    }

    public function test_superadmin_can_save_inr_and_usd_plan_prices(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->from('/superadmin/plan-prices')
            ->put('/superadmin/plan-prices', [
                'prices' => [
                    'fixed.monthly-nv' => ['amount_inr' => 6111, 'amount_usd' => 74.5],
                ],
            ])
            ->assertRedirect('/superadmin/plan-prices');

        $this->assertDatabaseHas('plan_prices', [
            'key' => 'fixed.monthly-nv',
            'amount_inr' => 6111,
        ]);

        $this->assertEquals(74.5, (float) PlanPrice::query()->where('key', 'fixed.monthly-nv')->value('amount_usd'));

        $this->get('/monthly-non-virtual-training')
            ->assertOk()
            ->assertSee('data-price="6111"', false)
            ->assertSee('data-price-usd="74.5"', false);
    }

    public function test_dashboard_prices_drive_server_quotes(): void
    {
        PlanPrice::query()->create([
            'key' => 'fixed.12-weeks-nv',
            'group' => 'Fixed non-virtual plans',
            'label' => '12 Weeks Non-Virtual Transformation',
            'amount_inr' => 8000,
            'amount_usd' => 96,
            'sort_order' => 1,
        ]);

        $quote = app(PlanPricing::class)->priceFor(['plan' => '12-weeks-nv']);

        $this->assertSame(8000, $quote['rupees']);
        $this->assertEquals(96.0, $quote['usd']);
    }

    public function test_superadmin_can_update_the_homepage_from_the_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/home')
            ->assertOk()
            ->assertSee('Hero slides')
            ->assertSee('Ready to get fit?');

        $this->actingAs($admin)
            ->put('/superadmin/home', [
                'title' => 'TFI Home edited',
                'meta_description' => 'Edited home description',
                'content' => [
                    'intro' => [
                        'heading' => 'Training that is',
                        'heading_accent' => 'built for you.',
                    ],
                    'contact' => [
                        'heading' => 'Start this week',
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/home');

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>TFI Home edited</title>', false)
            ->assertSee('Training that is')
            ->assertSee('built for you.')
            ->assertSee('Start this week');
    }

    public function test_superadmin_can_update_profile_name_and_photo(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'name' => 'Old Admin',
        ]);

        $this->actingAs($admin)
            ->put('/superadmin/profile', [
                'name' => 'New Admin Name',
                'photo' => UploadedFile::fake()->image('avatar.jpg', 120, 120),
            ])
            ->assertRedirect('/superadmin/profile');

        $admin->refresh();

        $this->assertSame('New Admin Name', $admin->name);
        $this->assertNotNull($admin->avatar);
        $this->assertFileExists(public_path($admin->avatar));

        @unlink(public_path($admin->avatar));
    }

    public function test_customers_cannot_open_the_cms(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)->get('/superadmin/pages')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/home')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/plan-prices')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/profile')->assertForbidden();
    }
}
