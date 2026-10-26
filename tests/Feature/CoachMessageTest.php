<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainee_message_appears_on_assigned_trainer_dashboard(): void
    {
        $trainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Anya']);
        $otherTrainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Ben']);
        $trainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'name' => 'Riya Trainee',
            'trainer_id' => $trainer->id,
        ]);

        $this->actingAs($trainee)
            ->from('/customer/messages')
            ->post('/customer/messages', [
                'body' => 'Can we add a rest day this week?',
            ])
            ->assertRedirect('/customer/messages');

        $this->assertDatabaseHas('coach_messages', [
            'trainee_id' => $trainee->id,
            'trainer_id' => $trainer->id,
            'body' => 'Can we add a rest day this week?',
        ]);

        $this->actingAs($trainee)
            ->get('/customer/messages')
            ->assertOk()
            ->assertSee('Can we add a rest day this week?');

        $this->actingAs($trainer)
            ->get('/admin/messages')
            ->assertOk()
            ->assertSee('Messages from trainees')
            ->assertSee('Riya Trainee')
            ->assertSee('Can we add a rest day this week?');

        $this->actingAs($otherTrainer)
            ->get('/admin/messages')
            ->assertOk()
            ->assertDontSee('Can we add a rest day this week?');
    }

    public function test_trainee_cannot_message_without_an_assigned_trainer(): void
    {
        $trainee = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'trainer_id' => null]);

        $this->actingAs($trainee)
            ->from('/customer/messages')
            ->post('/customer/messages', ['body' => 'Hello'])
            ->assertRedirect('/customer/messages')
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('coach_messages', 0);
    }

    public function test_trainer_reply_appears_on_trainee_dashboard(): void
    {
        $trainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Anya']);
        $otherTrainer = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $trainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'name' => 'Riya Trainee',
            'trainer_id' => $trainer->id,
        ]);

        $this->actingAs($trainer)
            ->from('/admin/messages')
            ->post('/admin/messages', [
                'trainee_id' => $trainee->id,
                'body' => 'Yes — take Wednesday as a rest day.',
            ])
            ->assertRedirect('/admin/messages');

        $this->assertDatabaseHas('coach_messages', [
            'trainee_id' => $trainee->id,
            'trainer_id' => $trainer->id,
            'from_role' => 'trainer',
            'body' => 'Yes — take Wednesday as a rest day.',
        ]);

        $this->actingAs($trainee)
            ->get('/customer/messages')
            ->assertOk()
            ->assertSee('Yes — take Wednesday as a rest day.')
            ->assertSee('Coach Anya');

        $this->actingAs($otherTrainer)
            ->from('/admin/dashboard')
            ->post('/admin/messages', [
                'trainee_id' => $trainee->id,
                'body' => 'This should not send.',
            ])
            ->assertNotFound();
    }
}
