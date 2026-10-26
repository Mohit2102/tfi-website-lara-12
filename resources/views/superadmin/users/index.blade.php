@extends('layouts.superadmin')

@section('title', $heading)
@section('heading', $heading)
@section('subheading', $subheading)

@section('content')
@php
    $initialsOf = fn ($name) => collect(preg_split('/\s+/', trim($name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
    $roleQuery = array_filter(['role' => $role]);
@endphp

<section class="stats">
    <a class="stat" href="{{ route('superadmin.users.index') }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-users"/></svg></span></div>
        <div class="stat-value">{{ $counts['all'] }}</div>
        <div class="stat-label">All users</div>
    </a>
    <a class="stat" href="{{ route('superadmin.users.index', ['role' => 'admin']) }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-shield-check"/></svg></span></div>
        <div class="stat-value">{{ $counts['admin'] }}</div>
        <div class="stat-label">Trainers</div>
    </a>
    <a class="stat" href="{{ route('superadmin.users.index', ['role' => 'customer']) }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-run"/></svg></span></div>
        <div class="stat-value">{{ $counts['customer'] }}</div>
        <div class="stat-label">Trainees</div>
    </a>
</section>

<section class="card flush">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-users"/></svg> {{ $heading }}</h3>
        <a class="btn-gold" href="{{ route('superadmin.users.create', $roleQuery) }}" style="height:38px; padding:0 1rem">
            <svg class="ic"><use href="#i-user-plus"/></svg> Add {{ $role ? \Illuminate\Support\Str::headline($role) : 'user' }}
        </a>
    </div>

    <form method="GET" action="{{ route('superadmin.users.index') }}" class="cms-toolbar" style="padding:0 1.5rem 1rem">
        @if ($role)
            <input type="hidden" name="role" value="{{ $role }}">
        @endif
        <div class="field" style="flex:1; min-width:220px">
            <label for="q">Search</label>
            <input id="q" name="q" value="{{ $search }}" placeholder="Name, email or phone">
        </div>
        <button class="btn-ghost" type="submit">Search</button>
    </form>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="td-name">
                                <div class="td-avatar">
                                    @if ($user->avatarUrl())
                                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                                    @else
                                        {{ $initialsOf($user->name) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="td-main">{{ $user->name }}</div>
                                    <div class="td-sub">{{ $user->phone ?: '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $user->email }}</td>
                        <td><span class="badge role-{{ $user->role }}">{{ \Illuminate\Support\Str::headline($user->role) }}</span></td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'st-active' : 'st-inactive' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="muted" style="font-size:.82rem; white-space:nowrap">{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a class="link" href="{{ route('superadmin.users.edit', $user) }}">Edit <svg class="ic"><use href="#i-arrow"/></svg></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No users match this list yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="form-actions" style="padding:1rem 1.5rem">
            @if ($users->previousPageUrl())
                <a class="btn-ghost" href="{{ $users->previousPageUrl() }}">Previous</a>
            @endif
            <span class="help">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
            @if ($users->nextPageUrl())
                <a class="btn-ghost" href="{{ $users->nextPageUrl() }}">Next</a>
            @endif
        </div>
    @endif
</section>
@endsection
