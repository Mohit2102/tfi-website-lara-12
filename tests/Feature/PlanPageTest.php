<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\HtmlPageRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class PlanPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_plan_pages_use_server_pricing_and_shared_checkout_script(): void
    {
        $this->get('/monthly-non-virtual-training')
            ->assertOk()
            ->assertSee('data-price="5999"', false)
            ->assertSee('<script src="/plan-checkout.js"></script>', false)
            ->assertSee('<script src="/tfi-currency.js"></script>', false);

        $this->get('/half-yearly-non-virtual-training')
            ->assertOk()
            ->assertSee('data-price="27999"', false)
            ->assertSee('<script src="/plan-checkout.js"></script>', false)
            ->assertSee('<script src="/tfi-currency.js"></script>', false);

        $this->get('/12-weeks-non-virtual-training')
            ->assertOk()
            ->assertSee('data-price="7499"', false)
            ->assertSee('<script src="/plan-checkout.js"></script>', false)
            ->assertSee('<script src="/tfi-currency.js"></script>', false);
    }

    public function test_every_rendered_html_page_loads_the_shared_checkout_script(): void
    {
        foreach (HtmlPageRenderer::PAGES as $page) {
            $url = $page === 'index' ? '/' : '/'.$page;

            $this->get($url)
                ->assertOk()
                ->assertSee('<script src="/plan-checkout.js"></script>', false)
                ->assertSee('<script src="/tfi-currency.js"></script>', false);
        }
    }

    public function test_login_registration_and_password_recovery_pages_render(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in to your account');
        $this->get('/register')->assertOk()->assertSee('Create your');
        $this->get('/forgot-password')->assertOk()->assertSee('Forgot your password?');
        $this->get('/reset-password/test-token?email=customer%40example.test')
            ->assertOk()
            ->assertSee('Choose a new password');
    }

    public function test_homepage_login_links_open_the_working_login_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="/login"', false);
    }

    public function test_public_pages_default_to_light_theme(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('<html lang="en" class="dark-mode">', false)
            ->assertSee('localStorage.getItem("tfi-theme") === "dark"', false)
            ->assertSee('root.classList.add("dark-mode")', false);
    }

    public function test_logged_in_user_sees_a_personalized_dashboard_link_on_homepage(): void
    {
        $customer = User::factory()->create([
            'name' => 'Taylor Customer',
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->actingAs($customer)
            ->get('/')
            ->assertOk()
            ->assertSee('href="/customer/dashboard">Hi, Taylor Customer</a>', false)
            ->assertDontSee('href="/login">Login/Signup</a>', false);
    }

    public function test_program_pages_send_visitors_to_login_and_show_dashboard_copy(): void
    {
        $this->get('/virtual-training')
            ->assertOk()
            ->assertSee('href="/login"', false)
            ->assertSee('FIND YOUR PLAN')
            ->assertSee('data-cms="kicker"', false);

        $this->get('/diabetes-management')
            ->assertOk()
            ->assertSee('href="/login"', false)
            ->assertSee('Diabetes Management')
            ->assertSee('data-cms-prefix="hero.title"', false);
    }

    public function test_logged_in_user_sees_a_personalized_dashboard_link_on_program_pages(): void
    {
        $customer = User::factory()->create([
            'name' => 'Taylor Customer',
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->actingAs($customer)
            ->get('/monthly-virtual-training')
            ->assertOk()
            ->assertSee('href="/customer/dashboard">Hi, Taylor Customer</a>', false)
            ->assertDontSee('href="/login">Login/Signup</a>', false);
    }

    public function test_logged_in_admin_sees_their_admin_dashboard_link_on_homepage(): void
    {
        $admin = User::factory()->create([
            'name' => 'Taylor Admin',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->get('/')
            ->assertOk()
            ->assertSee('href="/admin/dashboard">Hi, Taylor Admin</a>', false);
    }

    public function test_customer_login_returns_to_the_requested_plan_page(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => 'password',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
            'redirect' => '/monthly-virtual-training',
        ])->assertRedirect('/monthly-virtual-training');
    }

    public function test_customer_login_rejects_external_redirect_destinations(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
            'redirect' => '//example.test',
        ])->assertRedirect('/customer/dashboard');
    }

    public function test_a_customer_can_register_and_is_signed_in_without_becoming_an_admin(): void
    {
        $this->post('/register', [
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
            'role' => User::ROLE_SUPERADMIN,
            'redirect' => '/monthly-virtual-training',
        ])->assertRedirect('/monthly-virtual-training');

        $customer = User::where('email', 'new-customer@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($customer);
        $this->assertSame(User::ROLE_CUSTOMER, $customer->role);
        $this->assertTrue($customer->is_active);
        $this->assertTrue(Hash::check('strong-password', $customer->password));
    }

    public function test_login_rejects_inactive_accounts(): void
    {
        $customer = User::factory()->create([
            'email' => 'inactive@example.test',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->from('/login')->post('/login', [
            'email' => $customer->email,
            'password' => 'password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_password_reset_link_can_reset_a_customer_password(): void
    {
        Notification::fake();

        $customer = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => 'old-password',
        ]);

        $this->post('/forgot-password', ['email' => $customer->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($customer, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->assertIsString($token);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $customer->email,
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ])->assertRedirect('/login')
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check(
            'new-strong-password',
            $customer->fresh()->password
        ));
    }
}
