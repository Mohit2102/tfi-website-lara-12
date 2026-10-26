@extends('layouts.account')

@section('title', 'Trainees')
@section('heading', 'Trainees')
@section('subheading', 'Assigned trainees and their selected plans')

@section('content')
@php
    $trainees = $trainees ?? collect();
    $initialsOf = fn ($name) => collect(preg_split('/\s+/', trim($name ?: 'TFI')))
        ->filter()->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->take(2)->implode('');
@endphp
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-run"/></svg> Trainees and selected plans</h3>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Trainee</th>
                    <th>Contact</th>
                    <th>Selected plan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trainees as $trainee)
                    @php $activePlan = $trainee->plans->first(); @endphp
                    <tr>
                        <td>
                            <div class="td-name">
                                <div class="td-avatar">{{ $initialsOf($trainee->name) }}</div>
                                <div>
                                    <div class="td-main">{{ $trainee->name }}</div>
                                    <div class="td-sub">Joined {{ $trainee->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="muted">{{ $trainee->email }}<br>{{ $trainee->phone ?? '—' }}</td>
                        <td>
                            @if ($activePlan)
                                <div class="td-main">{{ $activePlan->name }}</div>
                                <div class="td-sub">Week {{ $activePlan->currentWeek() }} of {{ $activePlan->total_weeks }}</div>
                            @else
                                <span class="muted">No plan yet</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $activePlan ? 'st-active' : 'st-inactive' }}">
                                {{ $activePlan ? 'On plan' : 'No plan' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No trainees assigned to you yet. Superadmin assigns them under Assign trainers.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
