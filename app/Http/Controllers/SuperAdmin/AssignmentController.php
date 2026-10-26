<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(): View
    {
        return view('superadmin.assignments.index', [
            'trainers' => User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'trainees' => User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->with(['trainer', 'plans' => fn ($query) => $query->where('status', UserPlan::STATUS_ACTIVE)->latest('starts_on')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'trainer_id' => ['array'],
            'trainer_id.*' => ['nullable', Rule::exists('users', 'id')->where('role', User::ROLE_ADMIN)],
        ]);

        $assignments = $data['trainer_id'] ?? [];

        User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->get()
            ->each(function (User $trainee) use ($assignments) {
                if (! array_key_exists((string) $trainee->id, $assignments) && ! array_key_exists($trainee->id, $assignments)) {
                    return;
                }

                $trainerId = $assignments[$trainee->id] ?? $assignments[(string) $trainee->id] ?? null;
                $trainerId = filled($trainerId) ? (int) $trainerId : null;

                $trainee->forceFill(['trainer_id' => $trainerId])->save();

                UserPlan::query()
                    ->where('user_id', $trainee->id)
                    ->where('status', UserPlan::STATUS_ACTIVE)
                    ->update(['trainer_id' => $trainerId]);
            });

        return redirect()
            ->route('superadmin.assignments.index')
            ->with('success', 'Trainer assignments saved.');
    }
}
