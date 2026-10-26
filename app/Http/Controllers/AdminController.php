<?php

namespace App\Http\Controllers;

use App\Models\CoachMessage;
use App\Models\NutritionPlan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $me = Auth::user();

        $assigned = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('trainer_id', $me->id);
        $assignedIds = (clone $assigned)->pluck('id');

        $stats = [
            'total_customers' => (clone $assigned)->count(),
            'active_customers' => (clone $assigned)->where('is_active', true)->count(),
            'inactive_customers' => (clone $assigned)->where('is_active', false)->count(),
            'active_programs' => Schema::hasTable('user_plans')
                ? UserPlan::query()
                    ->where('status', UserPlan::STATUS_ACTIVE)
                    ->where(function ($query) use ($me, $assignedIds) {
                        $query->where('trainer_id', $me->id)
                            ->orWhereIn('user_id', $assignedIds);
                    })
                    ->count()
                : 0,
        ];

        $trainees = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('trainer_id', $me->id)
            ->with(['plans' => fn ($query) => $query->where('status', UserPlan::STATUS_ACTIVE)->latest('starts_on')])
            ->orderBy('name')
            ->get();

        $recentCustomers = $trainees->take(8);

        return view('dashboard.admin', compact('stats', 'recentCustomers', 'trainees'));
    }

    public function messages(): View
    {
        $board = $this->trainerBoard();

        return view('portal.admin.messages', $board);
    }

    public function nutrition(): View
    {
        $board = $this->trainerBoard();

        return view('portal.admin.nutrition', $board);
    }

    public function trainees(): View
    {
        $board = $this->trainerBoard();

        return view('portal.admin.trainees', ['trainees' => $board['trainees']]);
    }

    public function replyMessage(Request $request): RedirectResponse
    {
        $me = Auth::user();
        $data = $request->validate([
            'trainee_id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $trainee = $this->assignedTrainee((int) $data['trainee_id'], $me->id);

        CoachMessage::query()->create([
            'trainee_id' => $trainee->id,
            'trainer_id' => $me->id,
            'body' => $data['body'],
            'from_role' => CoachMessage::FROM_TRAINER,
        ]);

        CoachMessage::query()
            ->where('trainer_id', $me->id)
            ->where('trainee_id', $trainee->id)
            ->where('from_role', CoachMessage::FROM_TRAINEE)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()
            ->route('admin.messages.index')
            ->with('success', 'Reply sent to '.$trainee->name.'.');
    }

    public function shareNutrition(Request $request): RedirectResponse
    {
        $me = Auth::user();
        $data = $request->validate([
            'trainee_id' => ['required', 'integer'],
            'title' => ['nullable', 'string', 'max:160'],
            'document' => ['required', 'file', 'max:8192', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $trainee = $this->assignedTrainee((int) $data['trainee_id'], $me->id);
        $file = $request->file('document');
        $originalName = $file->getClientOriginalName();
        $path = $this->storeNutritionFile($file);

        NutritionPlan::query()->create([
            'trainee_id' => $trainee->id,
            'trainer_id' => $me->id,
            'title' => $data['title'] ?: pathinfo($originalName, PATHINFO_FILENAME),
            'path' => $path,
            'original_name' => $originalName,
        ]);

        return redirect()
            ->route('admin.nutrition.index')
            ->with('success', 'Nutrition plan shared with '.$trainee->name.'.');
    }

    public function destroyNutrition(NutritionPlan $nutritionPlan): RedirectResponse
    {
        if ((int) $nutritionPlan->trainer_id !== (int) Auth::id()) {
            abort(404);
        }

        $title = $nutritionPlan->title;
        $nutritionPlan->deleteStoredFile();
        $nutritionPlan->delete();

        return redirect()
            ->route('admin.nutrition.index')
            ->with('success', 'Removed '.$title.' from the trainee dashboard.');
    }

    private function trainerBoard(): array
    {
        $me = Auth::user();
        $trainees = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('trainer_id', $me->id)
            ->with(['plans' => fn ($query) => $query->where('status', UserPlan::STATUS_ACTIVE)->latest('starts_on')])
            ->orderBy('name')
            ->get();

        $unreadMessages = 0;
        $conversations = collect();

        if (Schema::hasTable('coach_messages')) {
            $unreadMessages = CoachMessage::query()
                ->where('trainer_id', $me->id)
                ->where('from_role', CoachMessage::FROM_TRAINEE)
                ->whereNull('read_at')
                ->count();

            $conversations = CoachMessage::query()
                ->with('trainee')
                ->where('trainer_id', $me->id)
                ->latest()
                ->limit(80)
                ->get()
                ->reverse()
                ->values()
                ->groupBy('trainee_id');
        }

        $nutritionPlans = Schema::hasTable('nutrition_plans')
            ? NutritionPlan::query()
                ->with('trainee')
                ->where('trainer_id', $me->id)
                ->latest()
                ->limit(20)
                ->get()
            : collect();

        return compact('trainees', 'conversations', 'unreadMessages', 'nutritionPlans');
    }

    private function assignedTrainee(int $traineeId, int $trainerId): User
    {
        return User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->where('trainer_id', $trainerId)
            ->whereKey($traineeId)
            ->firstOrFail();
    }

    private function storeNutritionFile(UploadedFile $file): string
    {
        $directory = public_path('uploads/nutrition');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/nutrition/'.$name;
    }
}
