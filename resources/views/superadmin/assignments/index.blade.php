@extends('layouts.superadmin')

@section('title', 'Assign trainers')
@section('heading', 'Assign trainers')
@section('subheading', 'Choose which trainer (admin) works with each trainee (customer)')

@section('content')
<section class="card flush">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-users"/></svg> Trainer → trainee</h3>
    </div>

    @if ($trainers->isEmpty())
        <p class="empty">Add an admin user first. Admins are trainers you can assign to trainees.</p>
    @elseif ($trainees->isEmpty())
        <p class="empty">No trainees yet. Customers appear here once they register.</p>
    @else
        <form method="POST" action="{{ route('superadmin.assignments.update') }}">
            @csrf
            @method('PUT')
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Trainee</th>
                            <th>Plan</th>
                            <th>Trainer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trainees as $trainee)
                            @php $activePlan = $trainee->plans->first(); @endphp
                            <tr>
                                <td>
                                    <div class="td-main">{{ $trainee->name }}</div>
                                    <div class="td-sub">{{ $trainee->email }}@if ($trainee->phone) · {{ $trainee->phone }}@endif</div>
                                </td>
                                <td class="muted">{{ $activePlan->name ?? 'No plan yet' }}</td>
                                <td>
                                    <div class="field" style="margin:0; min-width:220px">
                                        <select id="trainer-{{ $trainee->id }}" name="trainer_id[{{ $trainee->id }}]" aria-label="Trainer for {{ $trainee->name }}">
                                            <option value="">Unassigned</option>
                                            @foreach ($trainers as $trainer)
                                                <option value="{{ $trainer->id }}" @selected((string) old('trainer_id.'.$trainee->id, $trainee->trainer_id) === (string) $trainer->id)>
                                                    {{ $trainer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-actions" style="padding:1rem 1.5rem">
                <button class="btn-gold" type="submit">Save assignments</button>
            </div>
        </form>
    @endif
</section>
@endsection
