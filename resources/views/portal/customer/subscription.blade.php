@extends('layouts.account')

@section('title', 'Subscription')
@section('heading', 'Subscription')
@section('subheading', 'Your plan and payment')

@section('content')
<section class="card">
    @if ($planName)
        <div class="td-main" style="font-size:1.2rem">{{ $planName }}</div>
        <p class="prog-note" style="margin:.4rem 0 0">
            Status: {{ \Illuminate\Support\Str::headline($planStatus) }}
            · Week {{ min(max($planWeek, 1), $planTotal) }} of {{ $planTotal }}
            @if (!empty($plan['amount_inr'])) · ₹{{ number_format($plan['amount_inr']) }} @endif
        </p>
        @if ($payment)
            <p class="prog-note" style="margin:.6rem 0 0">
                Payment {{ $payment->source === 'dummy' ? 'demo' : 'paid' }}
                · {{ $payment->payment_id }}
                · {{ $payment->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
            </p>
        @endif
    @else
        <p class="prog-note">No active subscription yet. Choose a program on the website to get started.</p>
        <a href="{{ url('/#programs') }}" class="btn-gold" style="margin-top:1rem; width:max-content">Browse programs</a>
    @endif
</section>
@endsection
