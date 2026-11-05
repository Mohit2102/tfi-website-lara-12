<?php

namespace Tests\Feature;

use App\Models\SitePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private function superadmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
    }

    public function test_dashboard_uploads_are_converted_to_resized_webp(): void
    {
        $this->actingAs($this->superadmin())->put('/superadmin/home', [
            'title' => 'Home',
            'content' => ['founder' => ['name' => 'Santhosh M']],
            'uploads' => ['founder' => UploadedFile::fake()->image('founder.png', 2600, 1300)],
        ])->assertRedirect('/superadmin/home')->assertSessionHasNoErrors();

        $path = SitePage::query()->where('slug', 'index')->first()->content['founder']['image'];

        $this->assertStringStartsWith('uploads/home/', $path);
        $this->assertStringEndsWith('.webp', $path);
        $this->assertFileExists(public_path($path));
        $this->assertSame([1920, 960], array_slice(getimagesize(public_path($path)), 0, 2));

        @unlink(public_path($path));
    }

    public function test_profile_photos_are_stored_as_small_webp(): void
    {
        $admin = $this->superadmin();

        $this->actingAs($admin)->put('/superadmin/profile', [
            'name' => 'Admin',
            'photo' => UploadedFile::fake()->image('me.jpg', 1600, 1600),
        ])->assertSessionHasNoErrors();

        $avatar = $admin->fresh()->avatar;

        $this->assertStringEndsWith('.webp', $avatar);
        $this->assertSame(800, getimagesize(public_path($avatar))[0]);

        @unlink(public_path($avatar));
    }

    public function test_images_over_the_size_limit_are_rejected(): void
    {
        $this->actingAs($this->superadmin())->put('/superadmin/home', [
            'title' => 'Home',
            'content' => ['founder' => ['name' => 'Santhosh M']],
            'uploads' => ['founder' => UploadedFile::fake()->image('huge.jpg', 800, 600)->size(6000)],
        ])->assertSessionHasErrors(['uploads.founder' => 'Each image must be 5 MB or smaller — please compress or resize it and try again.']);

        $this->assertNull(SitePage::query()->where('slug', 'index')->first());
    }

    public function test_files_that_are_not_images_are_rejected(): void
    {
        $this->actingAs($this->superadmin())->put('/superadmin/pages/diabetes-management', [
            'title' => 'Diabetes',
            'content' => [],
            'uploads' => ['hero' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
        ])->assertSessionHasErrors(['uploads.hero' => 'Please upload a JPG, PNG, WebP, GIF, BMP or AVIF image.']);
    }
}
