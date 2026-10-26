@extends('layouts.superadmin')

@section('title', $user->exists ? 'Edit '.$user->name : 'Add user')
@section('heading', $user->exists ? 'Edit user' : 'Add user')
@section('subheading', $user->exists ? 'Update this account' : 'Create an admin or customer account')

@section('content')
@php
    $isSelf = $user->exists && $user->is(auth()->user());
    $back = route('superadmin.users.index', array_filter(['role' => $roleFilter]));
@endphp
<section class="card">
    <form method="POST" action="{{ $user->exists ? route('superadmin.users.update', $user) : route('superadmin.users.store') }}" enctype="multipart/form-data" class="cms-stack">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif
        @if ($roleFilter)
            <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
        @endif

        <div class="cms-grid">
            <div class="cms-block" style="justify-items:center; text-align:center">
                <div class="avatar avatar-lg">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                    @else
                        {{ collect(preg_split('/\s+/', trim($user->name ?: 'U')))->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('') ?: 'U' }}
                    @endif
                </div>
                <div class="field" style="width:100%; margin-top:.6rem">
                    <label for="photo">Photo</label>
                    <input id="photo" type="file" name="photo" accept="image/*">
                </div>
            </div>

            <div class="cms-stack">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" @disabled($isSelf)>
                        <option value="customer" @selected(old('role', $user->role) === 'customer')>Customer</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                        <option value="superadmin" @selected(old('role', $user->role) === 'superadmin')>Super admin</option>
                    </select>
                    @if ($isSelf)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                    @endif
                </div>
                {{-- <div class="field">
                    <label for="password">{{ $user->exists ? 'New password (optional)' : 'Password' }}</label>
                    <input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} minlength="8" autocomplete="new-password">
                </div> --}}
                <label class="check-row">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active ?? true)) @disabled($isSelf)>
                    Active
                </label>
                @if ($isSelf)
                    <input type="hidden" name="is_active" value="1">
                    <p class="help">You cannot change your own role or deactivate this account here.</p>
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">{{ $user->exists ? 'Save user' : 'Create user' }}</button>
            <a href="{{ $back }}" class="btn-ghost">Back to list</a>
        </div>
    </form>

    @if ($user->exists && ! $isSelf)
        <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" style="margin-top:1rem" onsubmit="return confirm('Remove this user permanently?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-ghost btn-logout">Delete user</button>
        </form>
    @endif
</section>
@endsection
