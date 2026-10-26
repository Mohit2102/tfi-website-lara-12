@extends('layouts.superadmin')

@section('title', 'Edit '.$name)
@section('heading', $name)
@section('subheading', 'Changes go live on the public website as soon as you save')

@section('content')
<section class="card">
    <div class="cms-toolbar">
        <p class="help">
            Title and description update the browser tab. The HTML below is the full page visitors see.
            Keep <code>.html</code> links as they are — the site rewrites them automatically.
        </p>
        <a class="btn-ghost" href="{{ $slug === 'index' ? url('/') : url('/'.$slug) }}" target="_blank" rel="noopener">
            View live <svg class="ic"><use href="#i-up-right"/></svg>
        </a>
    </div>

    <form method="POST" action="{{ route('superadmin.pages.update', $slug) }}" style="display:grid; gap:1.1rem; margin-top:1.2rem">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="title">Page title</label>
            <input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="180">
        </div>

        <div class="field">
            <label for="meta_description">Meta description</label>
            <input id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" maxlength="500">
        </div>

        <label class="check-row">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->is_published))>
            Published (uncheck to hide this page)
        </label>

        <div class="field">
            <label for="html">Page HTML</label>
            <textarea id="html" name="html" required>{{ old('html', $page->html) }}</textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">Save page</button>
            <a href="{{ route('superadmin.pages.index') }}" class="btn-ghost">Back to pages</a>
        </div>
    </form>

    @if ($page->exists)
        <form method="POST" action="{{ route('superadmin.pages.restore', $slug) }}" style="margin-top:1rem" onsubmit="return confirm('Restore the original HTML file and discard dashboard edits?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-ghost btn-logout">Restore original file</button>
        </form>
    @endif
</section>
@endsection
