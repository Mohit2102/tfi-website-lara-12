<?php

namespace Tests\Feature;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TrainersPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainers_page_renders_seeded_trainers_from_the_database(): void
    {
        $this->get('/trainers')
            ->assertOk()
            ->assertSee('Arjun Mehta')
            ->assertSee('Vikram Iyer')
            ->assertSee('data-cms="trainers.count">6<', false);
    }

    public function test_hidden_trainers_are_not_shown_and_the_count_follows(): void
    {
        Trainer::query()->where('name', 'Arjun Mehta')->update(['is_published' => false]);

        $this->get('/trainers')
            ->assertOk()
            ->assertDontSee('Arjun Mehta')
            ->assertSee('Priya Sharma')
            ->assertSee('data-cms="trainers.count">5<', false);
    }

    public function test_empty_team_shows_a_placeholder(): void
    {
        Trainer::query()->delete();

        $this->get('/trainers')
            ->assertOk()
            ->assertSee('Our coaching team will be introduced here soon.')
            ->assertDontSee('Priya Sharma');
    }

    public function test_superadmin_can_add_a_trainer_with_photo(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/trainers')
            ->assertOk()
            ->assertSee('Trainer cards');

        $this->actingAs($admin)
            ->post('/superadmin/trainers', [
                'name' => 'Meera Joshi',
                'experience' => '7+ Years',
                'bio' => 'Core strength and control for everyday movement.',
                'certification' => 'STOTT Pilates',
                'instagram_url' => 'https://instagram.com/meera',
                'sort_order' => 0,
                'is_published' => '1',
                'photo' => UploadedFile::fake()->image('meera.jpg', 600, 800),
            ])
            ->assertRedirect('/superadmin/trainers');

        $trainer = Trainer::query()->where('name', 'Meera Joshi')->firstOrFail();

        $this->assertFileExists(public_path($trainer->photo));

        $this->get('/trainers')
            ->assertOk()
            ->assertSee('<span class="tr-name-label">Name :</span><h3>Meera Joshi</h3>', false)
            ->assertSee('<p class="tr-bio">Core strength and control for everyday movement.</p>', false)
            ->assertDontSee('class="tr-tags"', false)
            ->assertSee('7+ YEARS')
            ->assertSee('https://instagram.com/meera', false)
            ->assertSee('uploads/trainers/', false)
            ->assertSee('data-cms="trainers.count">7<', false);

        @unlink(public_path($trainer->photo));
    }

    public function test_superadmin_can_reorder_toggle_and_delete_trainers(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $second = Trainer::query()->where('name', 'Priya Sharma')->firstOrFail();

        $this->actingAs($admin)
            ->patch("/superadmin/trainers/{$second->id}/move", ['direction' => 'up'])
            ->assertRedirect('/superadmin/trainers');

        $this->assertSame('Priya Sharma', Trainer::query()->ordered()->first()->name);

        $this->actingAs($admin)
            ->patch("/superadmin/trainers/{$second->id}/toggle")
            ->assertRedirect('/superadmin/trainers');

        $this->assertFalse($second->fresh()->is_published);

        $this->actingAs($admin)
            ->delete("/superadmin/trainers/{$second->id}")
            ->assertRedirect('/superadmin/trainers');

        $this->assertDatabaseMissing('trainers', ['id' => $second->id]);
    }

    public function test_combined_experience_is_calculated_from_published_trainers(): void
    {
        $this->get('/trainers')
            ->assertOk()
            ->assertSee('data-cms="trainers.experience">50+<', false);

        Trainer::query()->where('name', 'Vikram Iyer')->update(['is_published' => false]);

        $this->get('/trainers')
            ->assertSee('data-cms="trainers.experience">38+<', false);
    }

    public function test_superadmin_can_edit_trainers_page_copy(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

        $this->actingAs($admin)
            ->get('/superadmin/pages/trainers/edit')
            ->assertOk()
            ->assertSee('Heading accent')
            ->assertSee('Your Goals. Our Expertise.');

        $this->actingAs($admin)
            ->put('/superadmin/pages/trainers', [
                'title' => 'Our Coaches | TFI',
                'is_published' => '1',
                'content' => [
                    'hero' => ['kicker' => 'Coached by experts', 'title' => 'Meet the', 'title_accent' => 'Coaches', 'body' => 'Find your coach.'],
                    'stats' => ['coaches_label' => 'Coaches', 'experience_label' => 'Years', 'clients_value' => '2000+', 'clients_label' => 'Happy clients'],
                    'cards' => ['button' => 'Train with me', 'empty' => 'Coming soon'],
                    'cta' => ['heading' => 'Still deciding on a', 'heading_accent' => 'coach', 'heading_tail' => '?', 'body' => 'We will help.', 'button' => 'Talk to us'],
                ],
            ])
            ->assertRedirect('/superadmin/pages/trainers/edit');

        $this->get('/trainers')
            ->assertOk()
            ->assertSee('<title>Our Coaches | TFI</title>', false)
            ->assertSee('data-cms="hero.kicker">Coached by experts<', false)
            ->assertSee('data-cms="hero.title_accent">Coaches<', false)
            ->assertSee('data-cms="stats.clients_value">2000+<', false)
            ->assertSee('Train with me')
            ->assertSee('data-cms="cta.heading_accent">coach<', false)
            ->assertSee('data-cms="cta.button">Talk to us<', false)
            ->assertSee('Arjun Mehta')
            ->assertSee('data-cms="trainers.count">6<', false);
    }

    public function test_customers_cannot_manage_trainers(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)->get('/superadmin/trainers')->assertForbidden();
        $this->actingAs($customer)->post('/superadmin/trainers', ['name' => 'X'])->assertForbidden();
    }
}
