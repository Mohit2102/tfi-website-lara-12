@extends('layouts.account')

@section('title', 'Your trainer')
@section('heading', 'Your trainer')
@section('subheading', 'The coach assigned to you')

@section('content')
<section class="card">
    @if ($coach)
        <div class="coach-card">
            <div class="avatar coach-photo">
                @if ($coach->avatarUrl())
                    <img src="{{ $coach->avatarUrl() }}" alt="{{ $coach->name }}">
                @else
                    {{ $coachInitials ?: 'T' }}
                @endif
            </div>
            <div>
                <div class="role">Assigned trainer</div>
                <h3>{{ $coach->name }}</h3>
                <p class="prog-note" style="margin:.35rem 0 0">
                    {{ $coach->bio ?: 'This is your TFI coach for training, form checks and weekly guidance.' }}
                    @if ($coach->city)
                        · {{ $coach->city }}
                    @endif
                </p>
                <div class="coach-contacts">
                    @if ($coach->email)
                        <a href="mailto:{{ $coach->email }}"><svg class="ic"><use href="#i-msg"/></svg> {{ $coach->email }}</a>
                    @endif
                    @if ($coach->phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $coach->phone) }}"><svg class="ic"><use href="#i-phone"/></svg> {{ $coach->phone }}</a>
                    @endif
                    <a href="{{ route('customer.messages.index') }}"><svg class="ic"><use href="#i-msg"/></svg> Message coach</a>
                </div>
            </div>
        </div>
    @else
        <h3>Trainer not assigned yet</h3>
        <p class="prog-note">TFI will assign a trainer to you. Once superadmin pairs you, their profile will show here.</p>
    @endif
</section>
@endsection
