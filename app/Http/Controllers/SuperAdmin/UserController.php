<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $this->requestedRole($request);
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($role, fn ($query) => $query->where('role', $role))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $titles = [
            User::ROLE_ADMIN => ['Admins', 'Staff accounts that can manage customers'],
            User::ROLE_CUSTOMER => ['Customers', 'People who buy and follow training plans'],
            null => ['All users', 'Every account on the TFI portal'],
        ];

        [$heading, $subheading] = $titles[$role];

        return view('superadmin.users.index', [
            'users' => $users,
            'role' => $role,
            'search' => $search,
            'heading' => $heading,
            'subheading' => $subheading,
            'counts' => [
                'all' => User::count(),
                'admin' => User::where('role', User::ROLE_ADMIN)->count(),
                'customer' => User::where('role', User::ROLE_CUSTOMER)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $role = $this->requestedRole($request) ?: User::ROLE_CUSTOMER;

        return view('superadmin.users.form', [
            'user' => new User(['role' => $role, 'is_active' => true]),
            'roleFilter' => $this->requestedRole($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active');
        $data['avatar'] = $this->storePhoto($request->file('photo'));

        User::query()->create($data);

        return redirect()
            ->route('superadmin.users.index', array_filter(['role' => $this->listRole($request)]))
            ->with('success', 'User created.');
    }

    public function edit(Request $request, User $user): View
    {
        return view('superadmin.users.form', [
            'user' => $user,
            'roleFilter' => $this->requestedRole($request),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        unset($data['photo']);
        $data['is_active'] = $request->boolean('is_active');

        if ($user->is($request->user())) {
            $data['role'] = $user->role;
            $data['is_active'] = true;
        }

        if ($request->file('photo') instanceof UploadedFile) {
            $data['avatar'] = $this->storePhoto($request->file('photo'), $user->avatar);
        }

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('superadmin.users.edit', $user)
            ->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403, 'You cannot delete your own account.');

        $user->delete();

        return redirect()
            ->route('superadmin.users.index', array_filter(['role' => $this->listRole($request)]))
            ->with('success', 'User removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user): array
    {
        $passwordRules = $user
            ? ['nullable', 'string', 'min:8']
            : ['required', 'string', 'min:8'];

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_CUSTOMER])],
            'password' => $passwordRules,
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function requestedRole(Request $request): ?string
    {
        return $this->normalizeRole($request->query('role'));
    }

    private function listRole(Request $request): ?string
    {
        return $this->normalizeRole($request->input('role_filter', $request->query('role')));
    }

    private function normalizeRole(mixed $role): ?string
    {
        return in_array($role, [User::ROLE_ADMIN, User::ROLE_CUSTOMER], true) ? $role : null;
    }

    private function storePhoto(?UploadedFile $file, ?string $previous = null): ?string
    {
        if (! $file instanceof UploadedFile) {
            return $previous;
        }

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
