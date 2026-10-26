@extends('layouts.superadmin')

@section('title', $enquiry->name)
@section('heading', 'Enquiry details')
@section('subheading', 'Submitted '.$enquiry->created_at->format('d M Y, h:i A'))

@section('content')
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-user"/></svg> {{ $enquiry->name }}</h3>
        <span class="badge st-active">{{ $enquiry->program }}</span>
    </div>

    <div class="cms-grid">
        <div class="field">
            <label>Full name</label>
            <div class="help" style="font-size:1rem; color:var(--text)">{{ $enquiry->name }}</div>
        </div>
        <div class="field">
            <label>Email</label>
            <div class="help" style="font-size:1rem; color:var(--text)"><a class="link" href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></div>
        </div>
        <div class="field">
            <label>Phone</label>
            <div class="help" style="font-size:1rem; color:var(--text)"><a class="link" href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a></div>
        </div>
        <div class="field">
            <label>Program</label>
            <div class="help" style="font-size:1rem; color:var(--text)">{{ $enquiry->program }}</div>
        </div>
        <div class="field full">
            <label>Fitness goal / message</label>
            <div class="cms-block" style="white-space:pre-wrap; font-size:.95rem; line-height:1.6">{{ $enquiry->message ?: 'No message left.' }}</div>
        </div>
        <div class="field">
            <label>Received</label>
            <div class="help">{{ $enquiry->created_at->format('l, d M Y · h:i A') }}</div>
        </div>
        <div class="field">
            <label>Source IP</label>
            <div class="help">{{ $enquiry->ip_address ?: '—' }}</div>
        </div>
    </div>

    <div class="form-actions" style="margin-top:1.4rem">
        <a class="btn-ghost" href="{{ route('superadmin.enquiries.index') }}">Back to list</a>
        <form method="POST" action="{{ route('superadmin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Remove this enquiry?');">
            @csrf
            @method('DELETE')
            <button class="btn-signout" type="submit" style="background:var(--err-bg); color:var(--err-text); border-radius:10px; padding:.7rem 1rem;">Delete</button>
        </form>
    </div>
</section>
@endsection
