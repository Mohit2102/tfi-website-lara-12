@extends('layouts.account')

@section('title', 'Profile settings')
@section('heading', 'Profile settings')
@section('subheading', 'Update your photo and basic details')

@section('content')
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-user"/></svg> Your details</h3>
    </div>
    @include('dashboard.partials.profile-form', [
        'profileUser' => $user,
        'profileAction' => $profileAction,
    ])
</section>
@endsection
