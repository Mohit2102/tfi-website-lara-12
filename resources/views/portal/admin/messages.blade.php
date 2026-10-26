@extends('layouts.account')

@section('title', 'Trainee messages')
@section('heading', 'Trainee messages')
@section('subheading', 'Reply to the trainees assigned to you')

@section('content')
@php
    $trainees = $trainees ?? collect();
    $conversations = collect($conversations ?? []);
    $unreadMessages = (int) ($unreadMessages ?? 0);
@endphp
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-msg"/></svg> Messages from trainees</h3>
        @if ($unreadMessages > 0)
            <span class="chip">{{ $unreadMessages }} new</span>
        @endif
    </div>
    @forelse ($trainees as $trainee)
        @php $thread = $conversations->get($trainee->id, collect()); @endphp
        <div class="convo">
            <h4>{{ $trainee->name }}</h4>
            @if ($thread->isEmpty())
                <p class="empty">No messages yet. You can still send a note below.</p>
            @else
                <ul class="thread">
                    @foreach ($thread as $message)
                        <li class="{{ $message->isFromTrainer() ? 'from-trainer' : 'from-trainee' }}">
                            <b>{{ $message->isFromTrainer() ? 'You' : ($message->trainee?->name ?? 'Trainee') }}
                                <span> · {{ $message->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</span>
                            </b>
                            <p>{{ $message->body }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
            <form method="POST" action="{{ route('admin.messages.reply') }}" class="msg-form">
                @csrf
                <input type="hidden" name="trainee_id" value="{{ $trainee->id }}">
                <textarea name="body" maxlength="2000" required placeholder="Reply to {{ $trainee->name }}">{{ old('trainee_id') == $trainee->id ? old('body') : '' }}</textarea>
                <button class="btn-gold" type="submit" style="justify-self:start">Send reply</button>
            </form>
        </div>
    @empty
        <p class="empty">No trainees assigned yet. Superadmin assigns them under Assign trainers.</p>
    @endforelse
</section>
@endsection
