@extends('layouts.superadmin')

@section('title', 'Edit '.$name)
@section('heading', $name)
@section('subheading', 'Page copy for the public Trainers page. Changes go live as soon as you save')

@section('content')
<form method="POST" action="{{ route('superadmin.pages.update', $slug) }}" class="cms-stack">
    @csrf
    @method('PUT')

    <section class="card">
        <div class="cms-toolbar">
            <p class="help">Trainer cards, the coach count and combined years of experience come from <a class="link" href="{{ route('superadmin.trainers.index') }}">Website trainers</a>. Edit the surrounding copy here.</p>
            <a class="btn-ghost" href="{{ url('/'.$slug) }}" target="_blank" rel="noopener">View live <svg class="ic"><use href="#i-up-right"/></svg></a>
        </div>
        <div class="cms-grid" style="margin-top:1.2rem">
            <div class="field">
                <label for="title">Browser title</label>
                <input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="180">
            </div>
            <div class="field">
                <label for="meta_description">Meta description</label>
                <input id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" maxlength="500">
            </div>
        </div>
        <label class="check-row" style="margin-top:1rem">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $page->exists ? $page->is_published : true))>
            Published (uncheck to hide this page)
        </label>
    </section>

    <section class="card">
        <div class="card-head"><h3>Hero</h3></div>
        <div class="cms-grid">
            <div class="field full"><label>Kicker</label><input name="content[hero][kicker]" value="{{ old('content.hero.kicker', $content['hero']['kicker']) }}"></div>
            <div class="field"><label>Title</label><input name="content[hero][title]" value="{{ old('content.hero.title', $content['hero']['title']) }}"></div>
            <div class="field"><label>Title accent</label><input name="content[hero][title_accent]" value="{{ old('content.hero.title_accent', $content['hero']['title_accent']) }}"></div>
            <div class="field full"><label>Body</label><textarea class="short" name="content[hero][body]">{{ old('content.hero.body', $content['hero']['body']) }}</textarea></div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Hero stats</h3></div>
        <p class="help">The coach count and combined years are calculated from published trainers.</p>
        <div class="cms-grid" style="margin-top:1rem">
            <div class="field"><label>Coach count label</label><input name="content[stats][coaches_label]" value="{{ old('content.stats.coaches_label', $content['stats']['coaches_label']) }}"></div>
            <div class="field"><label>Experience label</label><input name="content[stats][experience_label]" value="{{ old('content.stats.experience_label', $content['stats']['experience_label']) }}"></div>
            <div class="field"><label>Clients value</label><input name="content[stats][clients_value]" value="{{ old('content.stats.clients_value', $content['stats']['clients_value']) }}"></div>
            <div class="field"><label>Clients label</label><input name="content[stats][clients_label]" value="{{ old('content.stats.clients_label', $content['stats']['clients_label']) }}"></div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Trainer cards</h3></div>
        <div class="cms-grid">
            <div class="field"><label>Card button</label><input name="content[cards][button]" value="{{ old('content.cards.button', $content['cards']['button']) }}"></div>
            <div class="field full"><label>Message when no trainers are published</label><input name="content[cards][empty]" value="{{ old('content.cards.empty', $content['cards']['empty']) }}"></div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Call to action</h3></div>
        <div class="cms-grid">
            <div class="field"><label>Heading</label><input name="content[cta][heading]" value="{{ old('content.cta.heading', $content['cta']['heading']) }}"></div>
            <div class="field"><label>Heading accent</label><input name="content[cta][heading_accent]" value="{{ old('content.cta.heading_accent', $content['cta']['heading_accent']) }}"></div>
            <div class="field full"><label>Heading ending</label><input name="content[cta][heading_tail]" value="{{ old('content.cta.heading_tail', $content['cta']['heading_tail']) }}"></div>
            <div class="field full"><label>Body</label><textarea class="short" name="content[cta][body]">{{ old('content.cta.body', $content['cta']['body']) }}</textarea></div>
            <div class="field"><label>Button</label><input name="content[cta][button]" value="{{ old('content.cta.button', $content['cta']['button']) }}"></div>
        </div>
    </section>

    <div class="form-actions">
        <button type="submit" class="btn-gold">Save page</button>
        <a href="{{ route('superadmin.trainers.index') }}" class="btn-ghost">Back to trainers</a>
    </div>
</form>

@if ($page->exists)
    <form method="POST" action="{{ route('superadmin.pages.restore', $slug) }}" style="margin-top:1rem" onsubmit="return confirm('Restore the original Trainers page copy and discard dashboard edits?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-ghost btn-logout">Restore original copy</button>
    </form>
@endif
@endsection
