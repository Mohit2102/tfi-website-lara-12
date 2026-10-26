@extends('layouts.account')

@section('title', 'Nutrition')
@section('heading', 'Nutrition plan')
@section('subheading', 'Documents your trainer shared with you')

@section('content')
<section class="card">
    @if ($nutritionPlans->isEmpty())
        <p class="prog-note">Your trainer has not shared a nutrition document yet. When they do, it will appear here.</p>
    @else
        <ul class="file-list">
            @foreach ($nutritionPlans as $planDoc)
                <li>
                    <div>
                        <div class="td-main">{{ $planDoc->title }}</div>
                        <div class="td-sub">
                            {{ $planDoc->original_name }}
                            · {{ $planDoc->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </div>
                    </div>
                    <a class="btn-ghost" href="{{ $planDoc->url() }}" target="_blank" rel="noopener">Open</a>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
