@extends('layouts.account')

@section('title', 'Progress')
@section('heading', 'Progress')
@section('subheading', 'This week activity and goals')

@section('content')
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-activity"/></svg> This week's activity</h3>
        <span class="chip">{{ $fmt($weekHours) }} hrs</span>
    </div>
    <div class="chart">
        @foreach ($week as $i => $d)
            <div class="col {{ $d['h'] > 0 ? 'on' : '' }}">
                <div class="b" style="height: {{ $d['h'] > 0 ? max(14, round($d['h'] / $maxH * 100)) : 4 }}%"></div>
                <span>{{ $d['d'] }}</span>
            </div>
        @endforeach
    </div>
</section>
<section class="card">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-trend"/></svg> Weekly goal progress</h3>
    </div>
    <div class="prog-row"><span>Overall</span><b>{{ $weekPct }}%</b></div>
    <div class="bar-wrap"><div class="bar-fill" style="width:{{ $weekPct }}%"></div></div>
    <p class="prog-note">
        @if ($goal > 0) {{ $done }} of {{ $goal }} sessions completed this week @else No weekly goal set yet @endif
    </p>
    <div class="prog-row"><span>Cardio</span><b>{{ $cardioPct }}%</b></div>
    <div class="bar-wrap"><div class="bar-fill" style="width:{{ $cardioPct }}%"></div></div>
    <div class="prog-row"><span>Strength</span><b>{{ $strengthPct }}%</b></div>
    <div class="bar-wrap" style="margin-bottom:0"><div class="bar-fill" style="width:{{ $strengthPct }}%"></div></div>
</section>
@endsection
