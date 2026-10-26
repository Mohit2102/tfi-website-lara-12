<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainer_can_update_basic_profile_details(): void
    {
        $trainer = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'name' => 'Old Coach',
            'email' => 'coach@example.com',
        ]);

        $this->actingAs($trainer)
            ->put('/admin/profile', [
                'name' => 'Coach Anya',
                'email' => 'anya@example.com',
                'phone' => '+91 90000 11111',
                'city' => 'Chennai',
                'gender' => 'female',
                'date_of_birth' => '1992-04-12',
                'bio' => 'Strength coach for TFI.',
                'photo' => UploadedFile::fake()->image('coach.jpg', 200, 200),
            ])
            ->assertRedirect('/admin/profile');

        $trainer->refresh();

        $this->assertSame('Coach Anya', $trainer->name);
        $this->assertSame('anya@example.com', $trainer->email);
        $this->assertSame('Chennai', $trainer->city);
        $this->assertSame('female', $trainer->gender);
        $this->assertSame('Strength coach for TFI.', $trainer->bio);
        $this->assertNotNull($trainer->avatar);
        $this->assertFileExists(public_path($trainer->avatar));

        $this->actingAs($trainer)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('Coach Anya')
            ->assertSee('Chennai')
            ->assertSee('Profile settings');

        File::delete(public_path($trainer->avatar));
    }

    public function test_trainee_can_update_basic_profile_details(): void
    {
        $trainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'name' => 'Riya',
            'email' => 'riya@example.com',
        ]);

        $this->actingAs($trainee)
            ->put('/customer/profile', [
                'name' => 'Riya Trainee',
                'email' => 'riya@example.com',
                'phone' => '+91 98888 22222',
                'city' => 'Coimbatore',
                'gender' => 'female',
                'bio' => 'Training for my first 10k.',
            ])
            ->assertRedirect('/customer/profile');

        $trainee->refresh();

        $this->assertSame('Riya Trainee', $trainee->name);
        $this->assertSame('Coimbatore', $trainee->city);
        $this->assertSame('Training for my first 10k.', $trainee->bio);

        $this->actingAs($trainee)
            ->get('/customer/profile')
            ->assertOk()
            ->assertSee('Riya Trainee')
            ->assertSee('Coimbatore')
            ->assertSee('Profile settings');
    }
}
