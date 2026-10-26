@extends('layouts.account')

@section('title', 'Schedule')
@section('heading', 'Schedule')
@section('subheading', 'Upcoming sessions with your coach')

@section('content')
<section class="card">
    <ul class="sched">
        @forelse ($sessions as $s)
            <li class="sess">
                <span class="tile"><svg class="ic"><use href="#{{ $iconFor(data_get($s, 'type')) }}"/></svg></span>
                <div class="sess-info">
                    <b>{{ data_get($s, 'title') }}</b>
                    <span>{{ \Illuminate\Support\Carbon::parse(data_get($s, 'starts_at'))->format('g:i A') }}@if (data_get($s, 'duration_min')) · {{ data_get($s, 'duration_min') }} min @endif</span>
                </div>
                <span class="day">{{ $dayLabel(data_get($s, 'starts_at')) }}</span>
            </li>
        @empty
            <li class="sess-empty">No upcoming sessions yet. Your coach will add them here.</li>
        @endforelse
    </ul>
</section>
@endsection
