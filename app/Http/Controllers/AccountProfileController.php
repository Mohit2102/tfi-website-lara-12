<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();

        return view('account.profile', [
            'user' => $user,
            'profileAction' => $user->isCustomer()
                ? route('customer.profile.update')
                : route('admin.profile.update'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->merge([
            'gender' => $request->filled('gender') ? $request->input('gender') : null,
            'date_of_birth' => $request->filled('date_of_birth') ? $request->input('date_of_birth') : null,
            'phone' => $request->filled('phone') ? $request->input('phone') : null,
            'city' => $request->filled('city') ? $request->input('city') : null,
            'bio' => $request->filled('bio') ? $request->input('bio') : null,
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:80'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:1920-01-01'],
            'bio' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        if ($request->file('photo') instanceof UploadedFile) {
            $user->avatar = $this->storePhoto($request->file('photo'), $user->avatar);
        }

        $user->save();

        $profile = $user->isCustomer() ? route('customer.profile.edit') : route('admin.profile.edit');

        return redirect()
            ->to($profile)
            ->with('success', 'Profile saved.');
    }

    private function storePhoto(UploadedFile $file, ?string $previous): string
    {
        $directory = public_path('uploads/avatars');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        if (is_string($previous) && str_starts_with($previous, 'uploads/avatars/') && is_file(public_path($previous))) {
            @unlink(public_path($previous));
        }

        return 'uploads/avatars/'.$name;
    }
}
