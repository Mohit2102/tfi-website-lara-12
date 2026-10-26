@extends('layouts.superadmin')

@section('title', 'Profile settings')
@section('heading', 'Profile settings')
@section('subheading', 'Update the name and photo shown in the superadmin panel')

@section('content')
<section class="card">
    <form method="POST" action="{{ route('superadmin.profile.update') }}" enctype="multipart/form-data" style="display:grid; gap:1.2rem">
        @csrf
        @method('PUT')

        <div class="cms-grid">
            <div class="cms-block" style="justify-items:center; text-align:center">
                <div class="avatar avatar-lg">
                    @if ($user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
                    @else
                        {{ collect(preg_split('/\s+/', trim($user->name)))->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('') }}
                    @endif
                </div>
                <div class="field" style="width:100%; margin-top:.6rem">
                    <label for="photo">Profile photo</label>
                    <input id="photo" type="file" name="photo" accept="image/*">
                </div>
                <p class="help">JPG, PNG or WEBP. Square photos look best.</p>
            </div>

            <div class="cms-stack">
                <div class="field">
                    <label for="name">Display name</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
                </div>
                <div class="field">
                    <label>Email</label>
                    <input value="{{ $user->email }}" disabled>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-gold">Save profile</button>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection
