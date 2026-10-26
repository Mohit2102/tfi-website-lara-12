@extends('layouts.account')

@section('title', 'My Program')
@section('heading', 'My Program')
@section('subheading', 'Your current training plan')

@section('content')
@if ($planName)
    <section class="card plan">
        <div class="plan-top">
            <div class="plan-ic"><svg class="ic"><use href="#i-dumbbell"/></svg></div>
            <div>
                <h3>{{ $planName }}</h3>
                <p class="prog-note" style="margin:0">Week {{ min(max($planWeek, 1), $planTotal) }} of {{ $planTotal }}@if ($currentPhase) — {{ $currentPhase }} Phase @endif</p>
                @if ($trainer)
                    <p class="prog-note" style="margin:.3rem 0 0">Trainer: {{ $trainer }}</p>
                @endif
            </div>
            <span class="badge-active">{{ \Illuminate\Support\Str::headline($planStatus) }}</span>
        </div>
        @if (!empty($plan['labels']))
            <p class="prog-note" style="margin:1rem 0 0">
                {{ implode(' · ', array_filter($plan['labels'])) }}
                @if (!empty($plan['amount_inr'])) · ₹{{ number_format($plan['amount_inr']) }} @endif
            </p>
        @endif
        <div class="phases">
            @foreach ($phases as $ph)
                <div class="phase {{ $ph['state'] }}"><b>Weeks {{ $ph['start'] }}–{{ $ph['end'] }}</b>{{ $ph['name'] }}</div>
            @endforeach
        </div>
    </section>
@else
    <section class="card">
        <h3>No active plan yet</h3>
        <p class="prog-note">Pick a training program on the website, complete checkout, and it will show up here.</p>
        <a href="{{ url('/#programs') }}" class="btn-gold" style="margin-top:1rem; width:max-content">Browse programs</a>
    </section>
@endif
@endsection
