@extends('layouts.superadmin')

@section('title', $trainer->exists ? 'Edit '.$trainer->name : 'Add trainer')
@section('heading', $trainer->exists ? 'Edit trainer' : 'Add trainer')
@section('subheading', 'This card appears on the public Trainers page')

@section('content')
<section class="card">
    <form method="POST" action="{{ $trainer->exists ? route('superadmin.trainers.update', $trainer) : route('superadmin.trainers.store') }}" enctype="multipart/form-data" class="cms-stack">
        @csrf
        @if ($trainer->exists)
            @method('PUT')
        @endif

        <div class="cms-grid">
            <div class="cms-block" style="align-content:start">
                <h4>Photo</h4>
                @if ($trainer->photoUrl())
                    <x-cms-image :src="$trainer->photoUrl()" remove-name="remove_photo" :alt="$trainer->name" img-style="height:220px; object-position:center top" />
                @else
                    <p class="help">No photo yet — the card shows a gold gradient with a placeholder icon.</p>
                @endif
                <div class="field">
                    <label for="photo">{{ $trainer->photoUrl() ? 'Replace photo' : 'Upload photo' }}</label>
                    <input id="photo" type="file" name="photo" accept="image/*">
                    <p class="help">Portrait works best (around 600 × 800). Max 4 MB.</p>
                </div>
            </div>

            <div class="cms-block" style="align-content:start">
                <h4>Basics</h4>
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" value="{{ old('name', $trainer->name) }}" required maxlength="120">
                </div>
                <div class="field">
                    <label for="specialization">Specialization</label>
                    <input id="specialization" name="specialization" value="{{ old('specialization', $trainer->specialization) }}" maxlength="120" placeholder="Strength & Conditioning">
                </div>
                <div class="field">
                    <label for="experience">Experience badge</label>
                    <input id="experience" name="experience" value="{{ old('experience', $trainer->experience) }}" maxlength="40" placeholder="8+ Years">
                </div>
                <div class="cms-grid">
                    <div class="field">
                        <label for="sort_order">Display order</label>
                        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $trainer->sort_order) }}">
                    </div>
                    <label class="check-row" style="align-self:end; padding-bottom:.9rem">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $trainer->is_published ?? true))>
                        Show on website
                    </label>
                </div>
            </div>
        </div>

        <div class="cms-block">
            <h4>Card details</h4>
            <div class="field">
                <label for="bio">Short bio</label>
                <textarea class="short" id="bio" name="bio" maxlength="600" placeholder="One or two sentences about how they coach.">{{ old('bio', $trainer->bio) }}</textarea>
            </div>
            <div class="field">
                <label for="tags">Tags</label>
                <input id="tags" name="tags" value="{{ old('tags', implode(', ', $trainer->tags ?? [])) }}" maxlength="300" placeholder="Strength Training, Powerlifting, Athletic Performance">
                <p class="help">Separate with commas. Three tags fit best on one card.</p>
            </div>
            <div class="cms-grid-3">
                <div class="field">
                    <label for="certification">Certification</label>
                    <input id="certification" name="certification" value="{{ old('certification', $trainer->certification) }}" maxlength="160" placeholder="ACE Certified Personal Trainer">
                </div>
                <div class="field">
                    <label for="highlight">Highlight</label>
                    <input id="highlight" name="highlight" value="{{ old('highlight', $trainer->highlight) }}" maxlength="160" placeholder="300+ clients trained">
                </div>
                <div class="field">
                    <label for="languages">Languages</label>
                    <input id="languages" name="languages" value="{{ old('languages', $trainer->languages) }}" maxlength="160" placeholder="English, Hindi">
                </div>
            </div>
        </div>

        <div class="cms-block">
            <h4>Social links</h4>
            <div class="cms-grid">
                <div class="field">
                    <label for="instagram_url">Instagram URL</label>
                    <input id="instagram_url" type="url" name="instagram_url" value="{{ old('instagram_url', $trainer->instagram_url) }}" placeholder="https://instagram.com/…">
                </div>
                <div class="field">
                    <label for="linkedin_url">LinkedIn URL</label>
                    <input id="linkedin_url" type="url" name="linkedin_url" value="{{ old('linkedin_url', $trainer->linkedin_url) }}" placeholder="https://linkedin.com/in/…">
                </div>
            </div>
            <p class="help">Leave empty to hide that icon on the card.</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">{{ $trainer->exists ? 'Save trainer' : 'Add trainer' }}</button>
            <a href="{{ route('superadmin.trainers.index') }}" class="btn-ghost">Back to list</a>
        </div>
    </form>

    @if ($trainer->exists)
        <form method="POST" action="{{ route('superadmin.trainers.destroy', $trainer) }}" style="margin-top:1rem" onsubmit="return confirm('Remove this trainer from the website?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-ghost btn-logout">Delete trainer</button>
        </form>
    @endif
</section>
@endsection
