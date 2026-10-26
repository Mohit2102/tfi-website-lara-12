@php
    $profileUser = $profileUser ?? auth()->user();
    $profileAction = $profileAction ?? '#';
@endphp
<form method="POST" action="{{ $profileAction }}" enctype="multipart/form-data" class="profile-form">
    @csrf
    @method('PUT')

    <div class="profile-photo">
        <div class="avatar">
            @if ($profileUser->avatarUrl())
                <img src="{{ $profileUser->avatarUrl() }}" alt="{{ $profileUser->name }}">
            @else
                {{ collect(preg_split('/\s+/', trim($profileUser->name ?: 'U')))->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('') ?: 'U' }}
            @endif
        </div>
        <label>
            <span>Profile photo</span>
            <input type="file" name="photo" accept="image/*">
        </label>
    </div>

    <div class="profile-grid">
        <label>
            <span>Full name</span>
            <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required maxlength="120">
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email', $profileUser->email) }}" required>
        </label>
        <label>
            <span>Phone</span>
            <input type="text" name="phone" value="{{ old('phone', $profileUser->phone) }}" maxlength="30" placeholder="+91 90000 00000">
        </label>
        <label>
            <span>City</span>
            <input type="text" name="city" value="{{ old('city', $profileUser->city) }}" maxlength="80" placeholder="Chennai">
        </label>
        <label>
            <span>Gender</span>
            <select name="gender">
                <option value="">Prefer not to say</option>
                <option value="male" @selected(old('gender', $profileUser->gender) === 'male')>Male</option>
                <option value="female" @selected(old('gender', $profileUser->gender) === 'female')>Female</option>
                <option value="other" @selected(old('gender', $profileUser->gender) === 'other')>Other</option>
            </select>
        </label>
        <label>
            <span>Date of birth</span>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($profileUser->date_of_birth)->format('Y-m-d')) }}">
        </label>
        <label class="full">
            <span>About</span>
            <textarea name="bio" maxlength="500" placeholder="A short note about you">{{ old('bio', $profileUser->bio) }}</textarea>
        </label>
    </div>

    <button class="btn-gold" type="submit" style="justify-self:start">Save profile</button>
</form>
