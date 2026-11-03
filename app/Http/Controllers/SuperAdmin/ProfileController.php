<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('superadmin.profile.edit', [
            'user' => request()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $user = $request->user();
        $user->name = $data['name'];

        if ($request->file('photo') instanceof UploadedFile) {
            $user->avatar = $this->storePhoto($request->file('photo'), $user->avatar);
        } elseif ($request->boolean('remove_photo')) {
            if (is_string($user->avatar) && str_starts_with($user->avatar, 'uploads/avatars/') && is_file(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $user->avatar = null;
        }

        $user->save();

        return redirect()
            ->route('superadmin.profile.edit')
            ->with('success', 'Profile updated.');
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
