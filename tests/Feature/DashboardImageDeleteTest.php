<?php

namespace Tests\Feature;

use App\Models\SitePage;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DashboardImageDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }

    public function test_editors_show_a_delete_icon_on_images(): void
    {
        $admin = $this->superadmin();

        $this->actingAs($admin)->get('/superadmin/home')
            ->assertOk()
            ->assertSee('data-img-del', false)
            ->assertSee('name="content[founder][image]"', false);

        $this->actingAs($admin)->get('/superadmin/pages/monthly-non-virtual-training/edit')
            ->assertOk()
            ->assertSee('data-img-del', false);
    }

    public function test_deleting_a_home_image_clears_it_and_removes_the_uploaded_file(): void
    {
        $admin = $this->superadmin();

        $this->actingAs($admin)->put('/superadmin/home', [
            'title' => 'Home',
            'content' => ['founder' => ['name' => 'Santhosh M']],
            'uploads' => ['founder' => UploadedFile::fake()->image('founder.jpg', 300, 300)],
        ])->assertRedirect('/superadmin/home');

        $uploaded = SitePage::query()->where('slug', 'index')->first()->content['founder']['image'];
        $this->assertStringStartsWith('uploads/home/', $uploaded);
        $this->assertFileExists(public_path($uploaded));

        $this->actingAs($admin)->put('/superadmin/home', [
            'title' => 'Home',
            'content' => ['founder' => ['image' => '']],
        ])->assertRedirect('/superadmin/home');

        $this->assertSame('', SitePage::query()->where('slug', 'index')->first()->content['founder']['image']);
        $this->assertFileDoesNotExist(public_path($uploaded));

        $this->get('/')->assertOk()->assertDontSee('images/santhosh-land.png', false);
    }

    public function test_deleting_a_program_gallery_image(): void
    {
        $admin = $this->superadmin();

        $this->actingAs($admin)->put('/superadmin/pages/monthly-non-virtual-training', [
            'title' => 'Monthly NV',
            'is_published' => '1',
            'content' => ['inside' => [['image' => '', 'alt' => 'Diet']]],
        ])->assertRedirect('/superadmin/pages/monthly-non-virtual-training/edit');

        $this->assertSame('', SitePage::query()->where('slug', 'monthly-non-virtual-training')->first()->content['inside'][0]['image']);
    }

    public function test_deleting_trainer_user_and_profile_photos(): void
    {
        $admin = $this->superadmin();

        $trainer = Trainer::query()->first();
        $trainer->update(['photo' => 'uploads/trainers/old.jpg']);

        $this->actingAs($admin)->put("/superadmin/trainers/{$trainer->id}", [
            'name' => $trainer->name,
            'is_published' => '1',
            'remove_photo' => '1',
        ])->assertRedirect();
        $this->assertNull($trainer->fresh()->photo);

        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'avatar' => 'uploads/avatars/old.jpg']);
        $this->actingAs($admin)->put("/superadmin/users/{$customer->id}", [
            'name' => $customer->name,
            'email' => $customer->email,
            'role' => User::ROLE_CUSTOMER,
            'is_active' => '1',
            'remove_photo' => '1',
        ])->assertRedirect();
        $this->assertNull($customer->fresh()->avatar);

        $admin->update(['avatar' => 'uploads/avatars/me.jpg']);
        $this->actingAs($admin)->put('/superadmin/profile', [
            'name' => $admin->name,
            'remove_photo' => '1',
        ])->assertRedirect('/superadmin/profile');
        $this->assertNull($admin->fresh()->avatar);
    }

    public function test_a_new_upload_wins_over_remove(): void
    {
        $admin = $this->superadmin();
        $trainer = Trainer::query()->first();

        $this->actingAs($admin)->put("/superadmin/trainers/{$trainer->id}", [
            'name' => $trainer->name,
            'is_published' => '1',
            'remove_photo' => '1',
            'photo' => UploadedFile::fake()->image('new.jpg', 300, 400),
        ])->assertRedirect();

        $photo = $trainer->fresh()->photo;
        $this->assertStringStartsWith('uploads/trainers/', $photo);

        @unlink(public_path($photo));
    }
}
