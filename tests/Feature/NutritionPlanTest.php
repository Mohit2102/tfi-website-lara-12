<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class NutritionPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_trainer_can_share_nutrition_document_with_assigned_trainee(): void
    {
        $trainer = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Coach Anya']);
        $otherTrainer = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $trainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'name' => 'Riya Trainee',
            'trainer_id' => $trainer->id,
        ]);
        $otherTrainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'trainer_id' => $otherTrainer->id,
        ]);

        $file = UploadedFile::fake()->create('week-1-meals.pdf', 120, 'application/pdf');

        $this->actingAs($trainer)
            ->from('/admin/nutrition')
            ->post('/admin/nutrition', [
                'trainee_id' => $trainee->id,
                'title' => 'Week 1 meal plan',
                'document' => $file,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('nutrition_plans', [
            'trainee_id' => $trainee->id,
            'trainer_id' => $trainer->id,
            'title' => 'Week 1 meal plan',
        ]);

        $plan = \App\Models\NutritionPlan::query()->first();
        $this->assertNotNull($plan);
        $this->assertFileExists(public_path($plan->path));

        $this->actingAs($trainee)
            ->get('/customer/nutrition')
            ->assertOk()
            ->assertSee('Week 1 meal plan')
            ->assertSee($plan->url(), false);

        $this->actingAs($otherTrainee)
            ->get('/customer/nutrition')
            ->assertOk()
            ->assertDontSee('Week 1 meal plan');

        $this->actingAs($otherTrainer)
            ->from('/admin/nutrition')
            ->post('/admin/nutrition', [
                'trainee_id' => $trainee->id,
                'document' => UploadedFile::fake()->create('other.pdf', 20, 'application/pdf'),
            ])
            ->assertNotFound();

        File::delete(public_path($plan->path));
    }

    public function test_trainer_can_delete_nutrition_plan_and_trainee_no_longer_sees_it(): void
    {
        $trainer = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherTrainer = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $trainee = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
            'trainer_id' => $trainer->id,
        ]);

        $this->actingAs($trainer)
            ->post('/admin/nutrition', [
                'trainee_id' => $trainee->id,
                'title' => 'Week 1 meal plan',
                'document' => UploadedFile::fake()->create('week-1-meals.pdf', 80, 'application/pdf'),
            ])
            ->assertRedirect();

        $plan = \App\Models\NutritionPlan::query()->first();
        $this->assertNotNull($plan);
        $path = public_path($plan->path);

        $this->actingAs($otherTrainer)
            ->delete('/admin/nutrition/'.$plan->id)
            ->assertNotFound();

        $this->assertDatabaseHas('nutrition_plans', ['id' => $plan->id]);

        $this->actingAs($trainer)
            ->delete('/admin/nutrition/'.$plan->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('nutrition_plans', ['id' => $plan->id]);
        $this->assertFileDoesNotExist($path);

        $this->flushSession();

        $this->actingAs($trainee)
            ->get('/customer/nutrition')
            ->assertOk()
            ->assertDontSee('Week 1 meal plan');
    }
}
