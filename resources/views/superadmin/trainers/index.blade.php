@extends('layouts.superadmin')

@section('title', 'Website trainers')
@section('heading', 'Website trainers')
@section('subheading', 'Cards shown on the public Trainers page. Changes go live immediately.')

@section('content')
@php
    $initialsOf = fn ($name) => collect(preg_split('/\s+/', trim($name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
@endphp

<section class="stats">
    <div class="stat">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-users"/></svg></span></div>
        <div class="stat-value">{{ $counts['all'] }}</div>
        <div class="stat-label">All trainers</div>
    </div>
    <div class="stat">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-check-circle"/></svg></span></div>
        <div class="stat-value">{{ $counts['live'] }}</div>
        <div class="stat-label">Visible on website</div>
    </div>
    <div class="stat">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-lock"/></svg></span></div>
        <div class="stat-value">{{ $counts['hidden'] }}</div>
        <div class="stat-label">Hidden</div>
    </div>
</section>

<section class="card flush">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-shield-check"/></svg> Trainer cards</h3>
        <div class="form-actions">
            <a href="{{ url('/trainers') }}" class="link" target="_blank" rel="noopener">View page <svg class="ic"><use href="#i-up-right"/></svg></a>
            <a class="btn-gold" href="{{ route('superadmin.trainers.create') }}" style="height:38px; padding:0 1rem">
                <svg class="ic"><use href="#i-user-plus"/></svg> Add trainer
            </a>
        </div>
    </div>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Trainer</th>
                    <th>Experience</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trainers as $trainer)
                    <tr>
                        <td style="white-space:nowrap">
                            <div class="form-actions" style="gap:.35rem">
                                <form method="POST" action="{{ route('superadmin.trainers.move', $trainer) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="icon-btn" style="width:34px; height:34px" aria-label="Move {{ $trainer->name }} up" @disabled($loop->first)>
                                        <svg class="ic" style="width:16px; height:16px"><use href="#i-up"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.trainers.move', $trainer) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="icon-btn" style="width:34px; height:34px" aria-label="Move {{ $trainer->name }} down" @disabled($loop->last)>
                                        <svg class="ic" style="width:16px; height:16px; transform:rotate(180deg)"><use href="#i-up"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td>
                            <div class="td-name">
                                <div class="td-avatar">
                                    @if ($trainer->photoUrl())
                                        <img src="{{ $trainer->photoUrl() }}" alt="{{ $trainer->name }}">
                                    @else
                                        {{ $initialsOf($trainer->name) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="td-main">{{ $trainer->name }}</div>
                                    <div class="td-sub">{{ $trainer->specialization ?: '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $trainer->experience ?: '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('superadmin.trainers.toggle', $trainer) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $trainer->is_published ? 'st-active' : 'st-inactive' }}" title="Click to {{ $trainer->is_published ? 'hide' : 'show' }}">
                                    {{ $trainer->is_published ? 'Visible' : 'Hidden' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a class="link" href="{{ route('superadmin.trainers.edit', $trainer) }}">Edit <svg class="ic"><use href="#i-arrow"/></svg></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">No trainers yet. Add one to show it on the Trainers page.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
