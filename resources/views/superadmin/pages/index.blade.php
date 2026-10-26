@extends('layouts.superadmin')

@section('title', 'Website pages')
@section('heading', 'Website pages')
@section('subheading', 'Edit every public HTML page from the dashboard')

@section('content')
<section class="card flush">
    <div class="card-head">
        <h3><svg class="ic"><use href="#i-file"/></svg> Public pages</h3>
        <a href="{{ url('/') }}" class="link">Open website <svg class="ic"><use href="#i-up-right"/></svg></a>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Page</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Source</th>
                    <th>Updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>
                            <div class="td-main">{{ $page['name'] }}</div>
                            @if ($page['title'])
                                <div class="td-sub">{{ $page['title'] }}</div>
                            @endif
                        </td>
                        <td class="muted">{{ $page['slug'] === 'index' ? '/' : '/'.$page['slug'] }}</td>
                        <td>
                            <span class="badge {{ $page['is_published'] ? 'st-active' : 'st-inactive' }}">
                                {{ $page['is_published'] ? 'Live' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="muted">{{ $page['customized'] ? 'Dashboard' : 'HTML file' }}</td>
                        <td class="muted">{{ $page['updated_at']?->format('d M Y H:i') ?? '—' }}</td>
                        <td>
                            <a class="link" href="{{ route('superadmin.pages.edit', $page['slug']) }}">Edit <svg class="ic"><use href="#i-arrow"/></svg></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
