@extends('layouts.account')

@section('title', 'Nutrition plans')
@section('heading', 'Nutrition plans')
@section('subheading', 'Share a document with one assigned trainee')

@section('content')
@php
    $trainees = $trainees ?? collect();
    $nutritionPlans = collect($nutritionPlans ?? []);
@endphp
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-clipboard"/></svg> Share nutrition plan</h3>
    </div>
    @if ($trainees->isEmpty())
        <p class="empty">Assign a trainee first, then share a nutrition document for them.</p>
    @else
        <form method="POST" action="{{ route('admin.nutrition.store') }}" enctype="multipart/form-data" class="share-form">
            @csrf
            <label>
                <span>Trainee</span>
                <select name="trainee_id" required>
                    @foreach ($trainees as $trainee)
                        <option value="{{ $trainee->id }}" @selected(old('trainee_id') == $trainee->id)>{{ $trainee->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Title (optional)</span>
                <input type="text" name="title" maxlength="160" value="{{ old('title') }}" placeholder="Week 1 meal plan">
            </label>
            <label class="full">
                <span>Document (PDF, Word or image)</span>
                <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            </label>
            <button class="btn-gold" type="submit">Share with trainee</button>
        </form>
    @endif
    @if ($nutritionPlans->isNotEmpty())
        <ul class="file-list" style="margin-top:1.2rem">
            @foreach ($nutritionPlans as $planDoc)
                <li>
                    <div>
                        <div class="td-main">{{ $planDoc->title }}</div>
                        <div class="td-sub">{{ $planDoc->trainee?->name }} · {{ $planDoc->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="file-actions">
                        <a class="btn-ghost" href="{{ $planDoc->url() }}" target="_blank" rel="noopener">Open</a>
                        <form method="POST" action="{{ route('admin.nutrition.destroy', $planDoc) }}" onsubmit="return confirm('Remove this nutrition plan from the trainee dashboard?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost btn-logout">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
