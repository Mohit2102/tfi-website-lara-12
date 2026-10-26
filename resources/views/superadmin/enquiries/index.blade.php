@extends('layouts.superadmin')

@section('title', 'Enquiries')
@section('heading', 'Enquiries')
@section('subheading', 'People who submitted Ready to get fit? on the website')

@section('content')
<section class="stats">
    <a class="stat" href="{{ route('superadmin.enquiries.index') }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-file"/></svg></span></div>
        <div class="stat-value">{{ $counts['all'] }}</div>
        <div class="stat-label">All enquiries</div>
    </a>
    <a class="stat" href="{{ route('superadmin.enquiries.index', ['status' => 'new']) }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-bell"/></svg></span></div>
        <div class="stat-value">{{ $counts['new'] }}</div>
        <div class="stat-label">New</div>
    </a>
    <a class="stat" href="{{ route('superadmin.enquiries.index', ['status' => 'read']) }}">
        <div class="stat-top"><span class="tile-ic"><svg class="ic"><use href="#i-check-circle"/></svg></span></div>
        <div class="stat-value">{{ $counts['read'] }}</div>
        <div class="stat-label">Read</div>
    </a>
</section>

<section class="card flush">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-file"/></svg> {{ $status === 'new' ? 'New enquiries' : ($status === 'read' ? 'Read enquiries' : 'All enquiries') }}</h3>
    </div>

    <form method="GET" action="{{ route('superadmin.enquiries.index') }}" class="cms-toolbar" style="padding:0 1.5rem 1rem">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div class="field" style="flex:1; min-width:220px">
            <label for="q">Search</label>
            <input id="q" name="q" value="{{ $search }}" placeholder="Name, email, phone, program or message">
        </div>
        <button class="btn-ghost" type="submit">Search</button>
    </form>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Person</th>
                    <th>Program</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Received</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($enquiries as $enquiry)
                    <tr>
                        <td>
                            <div class="td-main">{{ $enquiry->name }}</div>
                            <div class="td-sub">{{ $enquiry->email }}</div>
                            <div class="td-sub">{{ $enquiry->phone }}</div>
                        </td>
                        <td>{{ $enquiry->program }}</td>
                        <td class="muted">{{ \Illuminate\Support\Str::limit($enquiry->message ?: '—', 72) }}</td>
                        <td>
                            <span class="badge {{ $enquiry->isUnread() ? 'st-active' : 'st-inactive' }}">
                                {{ $enquiry->isUnread() ? 'New' : 'Read' }}
                            </span>
                        </td>
                        <td class="muted" style="font-size:.82rem; white-space:nowrap">{{ $enquiry->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <a class="link" href="{{ route('superadmin.enquiries.show', $enquiry) }}">Details <svg class="ic"><use href="#i-arrow"/></svg></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">No enquiries yet. Submissions from Ready to get fit? will show up here.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($enquiries->hasPages())
        <div class="form-actions" style="padding:1rem 1.5rem">
            @if ($enquiries->previousPageUrl())
                <a class="btn-ghost" href="{{ $enquiries->previousPageUrl() }}">Previous</a>
            @endif
            <span class="help">Page {{ $enquiries->currentPage() }} of {{ $enquiries->lastPage() }}</span>
            @if ($enquiries->nextPageUrl())
                <a class="btn-ghost" href="{{ $enquiries->nextPageUrl() }}">Next</a>
            @endif
        </div>
    @endif
</section>
@endsection
