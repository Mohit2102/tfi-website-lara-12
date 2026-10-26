@extends('layouts.account')

@section('title', 'Message coach')
@section('heading', 'Message coach')
@section('subheading', 'Ask your trainer about training, diet or schedule')

@section('content')
<section class="card">
    @if ($coach)
        <form method="POST" action="{{ route('customer.messages.store') }}" class="msg-form">
            @csrf
            <textarea name="body" maxlength="2000" required placeholder="Ask {{ $coach->name }} about your plan, form, diet or schedule">{{ old('body') }}</textarea>
            <button class="btn-gold" type="submit" style="justify-self:start">Send to {{ $coach->name }}</button>
        </form>
        @if ($messages->isNotEmpty())
            <ul class="thread">
                @foreach ($messages as $message)
                    <li class="{{ $message->isFromTrainer() ? 'from-trainer' : 'from-trainee' }}">
                        <b>{{ $message->isFromTrainer() ? ($coach->name ?? 'Trainer') : 'You' }} · {{ $message->created_at?->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</b>
                        <p>{{ $message->body }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    @else
        <p class="prog-note">Assign a trainer first — then you can message them here.</p>
    @endif
</section>
@endsection
