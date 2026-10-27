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

    public function test_superadmin_can_update_a_program_page_from_the_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/virtual-training/edit')
            ->assertOk()
            ->assertSee('Page heading')
            ->assertDontSee('Page HTML');

        $this->actingAs($admin)
            ->put('/superadmin/pages/virtual-training', [
                'title' => 'Virtual training edited',
                'meta_description' => 'Edited virtual description',
                'is_published' => '1',
                'content' => [
                    'kicker' => 'DASHBOARD PLANS',
                    'heading' => 'Pick the plan that',
                    'heading_accent' => 'starts this week.',
                ],
            ])
            ->assertRedirect('/superadmin/pages/virtual-training/edit');

        $this->get('/virtual-training')
            ->assertOk()
            ->assertSee('<title>Virtual training edited</title>', false)
            ->assertSee('DASHBOARD PLANS')
            ->assertSee('Pick the plan that')
            ->assertSee('starts this week.');
    }

    public function test_superadmin_can_update_the_12_week_journey_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/12-weeks-virtual-training/edit')
            ->assertOk()
            ->assertSee('What to expect')
            ->assertSee('Foundation');

        $this->actingAs($admin)
            ->put('/superadmin/pages/12-weeks-virtual-training', [
                'title' => '12 weeks virtual edited',
                'is_published' => '1',
                'content' => [
                    'journey' => [
                        'kicker' => 'Your roadmap',
                        'heading' => 'A 12-week',
                        'heading_accent' => 'plan',
                        'steps' => [
                            ['title' => 'Start strong', 'body' => 'Baseline week one.'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/12-weeks-virtual-training/edit');

        $this->get('/12-weeks-virtual-training')
            ->assertOk()
            ->assertSee('Your roadmap')
            ->assertSee('A 12-week')
            ->assertSee('plan')
            ->assertSee('Start strong')
            ->assertSee('Baseline week one.')
            ->assertSee('WEEKS 5–8');
    }

    public function test_superadmin_can_update_the_half_yearly_journey_and_hero_image(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/half-yearly-virtual-training/edit')
            ->assertOk()
            ->assertSee('What to expect')
            ->assertSee('Gallery image');

        $this->actingAs($admin)
            ->put('/superadmin/pages/half-yearly-virtual-training', [
                'title' => 'Half yearly virtual edited',
                'is_published' => '1',
                'content' => [
                    'journey' => [
                        'kicker' => 'Six-month roadmap',
                        'heading' => 'Your half-year',
                        'heading_accent' => 'path',
                        'steps' => [
                            ['title' => 'Settle in', 'body' => 'First two months of form.'],
                        ],
                    ],
                ],
                'uploads' => [
                    'hero' => UploadedFile::fake()->image('hero.png', 640, 480),
                ],
            ])
            ->assertRedirect('/superadmin/pages/half-yearly-virtual-training/edit');

        $this->get('/half-yearly-virtual-training')
            ->assertOk()
            ->assertSee('Six-month roadmap')
            ->assertSee('Your half-year')
            ->assertSee('path')
            ->assertSee('Settle in')
            ->assertSee('First two months of form.')
            ->assertSee('MONTHS 3–4')
            ->assertSee('uploads/programs/half-yearly-virtual-training/', false);
    }

    public function test_superadmin_can_update_the_monthly_non_virtual_formula_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/monthly-non-virtual-training/edit')
            ->assertOk()
            ->assertSee('The formula')
            ->assertSee('A good exercise plan');

        $this->actingAs($admin)
            ->put('/superadmin/pages/monthly-non-virtual-training', [
                'title' => 'Monthly NV',
                'is_published' => '1',
                'content' => [
                    'formula' => [
                        'kicker' => 'The method',
                        'heading' => 'Three pillars.',
                        'heading_accent' => 'One result.',
                        'body' => 'All three belong in the same month.',
                        'steps' => [
                            ['title' => 'Training first', 'body' => 'A day-wise workout map.'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/monthly-non-virtual-training/edit');

        $this->get('/monthly-non-virtual-training')
            ->assertOk()
            ->assertSee('The method')
            ->assertSee('Three pillars.')
            ->assertSee('One result.')
            ->assertSee('All three belong in the same month.')
            ->assertSee('Training first')
            ->assertSee('A day-wise workout map.')
            ->assertSee('Smart eating rules');
    }

    public function test_superadmin_can_update_the_12_week_non_virtual_formula_and_journey(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/12-weeks-non-virtual-training/edit')
            ->assertOk()
            ->assertSee('The formula')
            ->assertSee('What to expect')
            ->assertSee('Foundation');

        $this->actingAs($admin)
            ->put('/superadmin/pages/12-weeks-non-virtual-training', [
                'title' => '12 weeks NV',
                'is_published' => '1',
                'content' => [
                    'formula' => [
                        'kicker' => 'Studio formula',
                        'heading_accent' => 'One studio plan.',
                    ],
                    'journey' => [
                        'heading' => 'Your studio',
                        'heading_accent' => 'path',
                        'steps' => [
                            ['title' => 'Settle in', 'body' => 'Week one baseline at the studio.'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/12-weeks-non-virtual-training/edit');

        $this->get('/12-weeks-non-virtual-training')
            ->assertOk()
            ->assertSee('Studio formula')
            ->assertSee('One studio plan.')
            ->assertSee('Your studio')
            ->assertSee('path')
            ->assertSee('Settle in')
            ->assertSee('Week one baseline at the studio.')
            ->assertSee('WEEKS 5–8');
    }

    public function test_superadmin_can_update_the_half_yearly_non_virtual_formula_and_journey(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/half-yearly-non-virtual-training/edit')
            ->assertOk()
            ->assertSee('The formula')
            ->assertSee('What to expect')
            ->assertSee('Your six-month');

        $this->actingAs($admin)
            ->put('/superadmin/pages/half-yearly-non-virtual-training', [
                'title' => 'Half yearly NV',
                'is_published' => '1',
                'content' => [
                    'formula' => [
                        'kicker' => 'Studio formula',
                        'heading_accent' => 'One studio plan.',
                    ],
                    'journey' => [
                        'heading' => 'Your half-year',
                        'heading_accent' => 'path',
                        'steps' => [
                            ['title' => 'Settle in', 'body' => 'First two months in the studio.'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/half-yearly-non-virtual-training/edit');

        $this->get('/half-yearly-non-virtual-training')
            ->assertOk()
            ->assertSee('Studio formula')
            ->assertSee('One studio plan.')
            ->assertSee('Your half-year')
            ->assertSee('path')
            ->assertSee('Settle in')
            ->assertSee('First two months in the studio.')
            ->assertSee('MONTHS 3–4');
    }

    public function test_superadmin_can_replace_non_virtual_plan_gallery_images(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->put('/superadmin/pages/monthly-non-virtual-training', [
                'title' => 'Monthly NV',
                'is_published' => '1',
                'content' => [
                    'inside' => [
                        ['alt' => 'New diet sample'],
                    ],
                ],
                'uploads' => [
                    'inside' => [
                        UploadedFile::fake()->image('diet.jpg', 400, 300),
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/monthly-non-virtual-training/edit');

        $this->get('/monthly-non-virtual-training')
            ->assertOk()
            ->assertSee('uploads/programs/monthly-non-virtual-training/', false)
            ->assertSee('New diet sample', false);
    }

    public function test_superadmin_can_upload_an_mvp_gallery_image_on_a_program_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/diabetes-management/edit')
            ->assertOk()
            ->assertSee('Gallery image');

        $this->actingAs($admin)
            ->put('/superadmin/pages/diabetes-management', [
                'title' => 'Diabetes',
                'is_published' => '1',
                'content' => [
                    'hero' => [
                        'image_alt' => 'Diabetes gallery photo',
                    ],
                ],
                'uploads' => [
                    'hero' => UploadedFile::fake()->image('gallery.jpg', 640, 480),
                ],
            ])
            ->assertRedirect('/superadmin/pages/diabetes-management/edit');

        $this->get('/diabetes-management')
            ->assertOk()
            ->assertSee('uploads/programs/diabetes-management/', false)
            ->assertSee('Diabetes gallery photo', false)
            ->assertSee('class="mvp-stage-img"', false);
    }

    public function test_superadmin_can_update_specialized_program_journey_and_tabs(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/diabetes-management/edit')
            ->assertOk()
            ->assertSee('What to expect')
            ->assertSee('Overview')
            ->assertSee('Our approach')
            ->assertSee('Safety');

        $this->actingAs($admin)
            ->put('/superadmin/pages/diabetes-management', [
                'title' => 'Diabetes',
                'is_published' => '1',
                'content' => [
                    'journey' => [
                        'kicker' => 'Your path',
                        'heading' => 'Your',
                        'heading_accent' => 'roadmap',
                        'steps' => [
                            ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Listen', 'body' => 'We start with your history, not a workout.'],
                        ],
                    ],
                    'overview' => [
                        'tab' => 'At a glance',
                        'heading' => 'Built for people who want better days.',
                        'cards' => [
                            ['body' => 'Living with type 2 diabetes and ready to train'],
                        ],
                    ],
                    'approach' => [
                        'tab' => 'How we work',
                        'heading_accent' => 'coach you',
                        'cards' => [
                            ['title' => 'Strength first', 'body' => 'Muscle work that supports glucose use.'],
                        ],
                    ],
                    'safety' => [
                        'tab' => 'Care first',
                        'heading' => 'Safety stays first.',
                        'items' => [
                            'We never change your medication',
                        ],
                        'note' => 'Lifestyle support only, not medical treatment.',
                    ],
                ],
            ])
            ->assertRedirect('/superadmin/pages/diabetes-management/edit');

        $this->get('/diabetes-management')
            ->assertOk()
            ->assertSee('Your path')
            ->assertSee('roadmap')
            ->assertSee('Listen')
            ->assertSee('We start with your history, not a workout.')
            ->assertSee('At a glance')
            ->assertSee('Built for people who want better days.')
            ->assertSee('Living with type 2 diabetes and ready to train')
            ->assertSee('How we work')
            ->assertSee('coach you')
            ->assertSee('Strength first')
            ->assertSee('Muscle work that supports glucose use.')
            ->assertSee('Care first')
            ->assertSee('Safety stays first.')
            ->assertSee('We never change your medication')
            ->assertSee('Lifestyle support only, not medical treatment.');
    }

    public function test_customers_cannot_open_the_cms(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)->get('/superadmin/pages')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/home')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/plan-prices')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/profile')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/enquiries')->assertForbidden();
        $this->actingAs($customer)->get('/superadmin/assignments')->assertForbidden();
    }
}
