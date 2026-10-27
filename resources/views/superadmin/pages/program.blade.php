@extends('layouts.superadmin')

@section('title', 'Edit '.$name)
@section('heading', $name)
@section('subheading', 'Dashboard fields go live on this program page as soon as you save')

@section('content')
<form method="POST" action="{{ route('superadmin.pages.update', $slug) }}" class="cms-stack" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <section class="card">
        <div class="cms-toolbar">
            <p class="help">Edit the public copy for this program. Prices stay on Plan prices. Login/Signup updates automatically for signed-in visitors.</p>
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

    @if ($layout === 'listing')
        <section class="card">
            <div class="card-head"><h3>Page heading</h3></div>
            <div class="cms-grid">
                <div class="field full"><label>Kicker</label><input name="content[kicker]" value="{{ old('content.kicker', $content['kicker']) }}"></div>
                <div class="field"><label>Heading</label><input name="content[heading]" value="{{ old('content.heading', $content['heading']) }}"></div>
                <div class="field"><label>Heading accent</label><input name="content[heading_accent]" value="{{ old('content.heading_accent', $content['heading_accent']) }}"></div>
                <div class="field full"><label>Body</label><textarea class="short" name="content[body]">{{ old('content.body', $content['body']) }}</textarea></div>
            </div>
        </section>
        @foreach ($content['cards'] as $i => $card)
            <section class="card">
                <div class="card-head"><h3>Plan {{ $i + 1 }}</h3></div>
                <div class="cms-grid">
                    <div class="field"><label>Badge</label><input name="content[cards][{{ $i }}][badge]" value="{{ old('content.cards.'.$i.'.badge', $card['badge']) }}"></div>
                    <div class="field"><label>Kicker</label><input name="content[cards][{{ $i }}][kicker]" value="{{ old('content.cards.'.$i.'.kicker', $card['kicker']) }}"></div>
                    <div class="field full"><label>Title</label><input name="content[cards][{{ $i }}][title]" value="{{ old('content.cards.'.$i.'.title', $card['title']) }}"></div>
                    <div class="field full"><label>Body</label><textarea class="short" name="content[cards][{{ $i }}][body]">{{ old('content.cards.'.$i.'.body', $card['body']) }}</textarea></div>
                    <div class="field full"><label>Button</label><input name="content[cards][{{ $i }}][cta]" value="{{ old('content.cards.'.$i.'.cta', $card['cta']) }}"></div>
                    @foreach ($card['features'] as $f => $feature)
                        <div class="field"><label>Feature {{ $f + 1 }}</label><input name="content[cards][{{ $i }}][features][{{ $f }}]" value="{{ old('content.cards.'.$i.'.features.'.$f, $feature) }}"></div>
                    @endforeach
                </div>
            </section>
        @endforeach
    @elseif ($layout === 'personal')
        <section class="card">
            <div class="card-head"><h3>Intro</h3></div>
            <div class="cms-grid">
                <div class="field full"><label>Kicker</label><input name="content[intro][kicker]" value="{{ old('content.intro.kicker', $content['intro']['kicker']) }}"></div>
                <div class="field"><label>Heading</label><input name="content[intro][heading]" value="{{ old('content.intro.heading', $content['intro']['heading']) }}"></div>
                <div class="field"><label>Heading accent</label><input name="content[intro][heading_accent]" value="{{ old('content.intro.heading_accent', $content['intro']['heading_accent']) }}"></div>
                <div class="field full"><label>Body</label><textarea class="short" name="content[intro][body]">{{ old('content.intro.body', $content['intro']['body']) }}</textarea></div>
            </div>
        </section>
        @foreach ($content['cards'] as $i => $card)
            <section class="card">
                <div class="card-head"><h3>Program {{ $i + 1 }}</h3></div>
                <div class="cms-grid">
                    <div class="field"><label>Label</label><input name="content[cards][{{ $i }}][label]" value="{{ old('content.cards.'.$i.'.label', $card['label']) }}"></div>
                    <div class="field full"><label>Title</label><input name="content[cards][{{ $i }}][title]" value="{{ old('content.cards.'.$i.'.title', $card['title']) }}"></div>
                    <div class="field full"><label>Body</label><textarea class="short" name="content[cards][{{ $i }}][body]">{{ old('content.cards.'.$i.'.body', $card['body']) }}</textarea></div>
                </div>
            </section>
        @endforeach
    @elseif ($layout === 'showcase')
        <section class="card">
            <div class="card-head"><h3>Hero and price copy</h3></div>
            <div class="cms-grid">
                <div class="field full"><label>Kicker</label><input name="content[hero][kicker]" value="{{ old('content.hero.kicker', $content['hero']['kicker']) }}"></div>
                <div class="field"><label>Title</label><input name="content[hero][title]" value="{{ old('content.hero.title', $content['hero']['title']) }}"></div>
                <div class="field"><label>Title accent</label><input name="content[hero][title_accent]" value="{{ old('content.hero.title_accent', $content['hero']['title_accent']) }}"></div>
                <div class="field full"><label>Lead</label><textarea class="short" name="content[hero][lead]">{{ old('content.hero.lead', $content['hero']['lead']) }}</textarea></div>
                <div class="field full"><label>Body</label><textarea class="short" name="content[hero][body]">{{ old('content.hero.body', $content['hero']['body']) }}</textarea></div>
                <div class="field"><label>Price label</label><input name="content[hero][price_label]" value="{{ old('content.hero.price_label', $content['hero']['price_label']) }}"></div>
                <div class="field"><label>Price note</label><input name="content[hero][price_per]" value="{{ old('content.hero.price_per', $content['hero']['price_per']) }}"></div>
            </div>
        </section>
        @if (! empty($content['formula']))
            <section class="card">
                <div class="card-head"><h3>The formula</h3></div>
                <div class="cms-grid">
                    <div class="field full"><label>Kicker</label><input name="content[formula][kicker]" value="{{ old('content.formula.kicker', $content['formula']['kicker']) }}"></div>
                    <div class="field"><label>Heading</label><input name="content[formula][heading]" value="{{ old('content.formula.heading', $content['formula']['heading']) }}"></div>
                    <div class="field"><label>Heading accent</label><input name="content[formula][heading_accent]" value="{{ old('content.formula.heading_accent', $content['formula']['heading_accent']) }}"></div>
                    <div class="field full"><label>Body</label><textarea class="short" name="content[formula][body]">{{ old('content.formula.body', $content['formula']['body']) }}</textarea></div>
                </div>
            </section>
            @foreach ($content['formula']['steps'] as $i => $step)
                <section class="card">
                    <div class="card-head"><h3>Point {{ $i + 1 }}</h3></div>
                    <div class="cms-grid">
                        <div class="field"><label>Number</label><input name="content[formula][steps][{{ $i }}][number]" value="{{ old('content.formula.steps.'.$i.'.number', $step['number']) }}"></div>
                        <div class="field full"><label>Title</label><input name="content[formula][steps][{{ $i }}][title]" value="{{ old('content.formula.steps.'.$i.'.title', $step['title']) }}"></div>
                        <div class="field full"><label>Body</label><textarea class="short" name="content[formula][steps][{{ $i }}][body]">{{ old('content.formula.steps.'.$i.'.body', $step['body']) }}</textarea></div>
                    </div>
                </section>
            @endforeach
        @endif
        @if (! empty($content['journey']))
            <section class="card">
                <div class="card-head"><h3>What to expect</h3></div>
                <div class="cms-grid">
                    <div class="field full"><label>Kicker</label><input name="content[journey][kicker]" value="{{ old('content.journey.kicker', $content['journey']['kicker']) }}"></div>
                    <div class="field"><label>Heading</label><input name="content[journey][heading]" value="{{ old('content.journey.heading', $content['journey']['heading']) }}"></div>
                    <div class="field"><label>Heading accent</label><input name="content[journey][heading_accent]" value="{{ old('content.journey.heading_accent', $content['journey']['heading_accent']) }}"></div>
                </div>
            </section>
            @foreach ($content['journey']['steps'] as $i => $step)
                <section class="card">
                    <div class="card-head"><h3>Phase {{ $i + 1 }}</h3></div>
                    <div class="cms-grid">
                        <div class="field"><label>Number</label><input name="content[journey][steps][{{ $i }}][number]" value="{{ old('content.journey.steps.'.$i.'.number', $step['number']) }}"></div>
                        <div class="field"><label>Period</label><input name="content[journey][steps][{{ $i }}][period]" value="{{ old('content.journey.steps.'.$i.'.period', $step['period']) }}"></div>
                        <div class="field full"><label>Title</label><input name="content[journey][steps][{{ $i }}][title]" value="{{ old('content.journey.steps.'.$i.'.title', $step['title']) }}"></div>
                        <div class="field full"><label>Body</label><textarea class="short" name="content[journey][steps][{{ $i }}][body]">{{ old('content.journey.steps.'.$i.'.body', $step['body']) }}</textarea></div>
                    </div>
                </section>
            @endforeach
        @endif
    @else
        <section class="card">
            <div class="card-head"><h3>Hero</h3></div>
            <div class="cms-grid">
                <div class="field"><label>Title</label><input name="content[hero][title]" value="{{ old('content.hero.title', $content['hero']['title']) }}"></div>
                <div class="field"><label>Title accent</label><input name="content[hero][title_accent]" value="{{ old('content.hero.title_accent', $content['hero']['title_accent']) }}"></div>
                <div class="field full"><label>Body</label><textarea class="short" name="content[hero][body]">{{ old('content.hero.body', $content['hero']['body']) }}</textarea></div>
                @foreach ($content['hero']['bullets'] as $i => $bullet)
                    <div class="field full"><label>Bullet {{ $i + 1 }}</label><input name="content[hero][bullets][{{ $i }}]" value="{{ old('content.hero.bullets.'.$i, $bullet) }}"></div>
                @endforeach
            </div>
        </section>
        @if (! empty($content['journey']))
            <section class="card">
                <div class="card-head"><h3>What to expect</h3></div>
                <div class="cms-grid">
                    <div class="field full"><label>Kicker</label><input name="content[journey][kicker]" value="{{ old('content.journey.kicker', $content['journey']['kicker']) }}"></div>
                    <div class="field"><label>Heading</label><input name="content[journey][heading]" value="{{ old('content.journey.heading', $content['journey']['heading']) }}"></div>
                    <div class="field"><label>Heading accent</label><input name="content[journey][heading_accent]" value="{{ old('content.journey.heading_accent', $content['journey']['heading_accent']) }}"></div>
                </div>
            </section>
            @foreach ($content['journey']['steps'] as $i => $step)
                <section class="card">
                    <div class="card-head"><h3>Phase {{ $i + 1 }}</h3></div>
                    <div class="cms-grid">
                        <div class="field"><label>Number</label><input name="content[journey][steps][{{ $i }}][number]" value="{{ old('content.journey.steps.'.$i.'.number', $step['number']) }}"></div>
                        <div class="field"><label>Period</label><input name="content[journey][steps][{{ $i }}][period]" value="{{ old('content.journey.steps.'.$i.'.period', $step['period']) }}"></div>
                        <div class="field full"><label>Title</label><input name="content[journey][steps][{{ $i }}][title]" value="{{ old('content.journey.steps.'.$i.'.title', $step['title']) }}"></div>
                        <div class="field full"><label>Body</label><textarea class="short" name="content[journey][steps][{{ $i }}][body]">{{ old('content.journey.steps.'.$i.'.body', $step['body']) }}</textarea></div>
                    </div>
                </section>
            @endforeach
        @endif
        @if (! empty($content['overview']))
            <section class="card">
                <div class="card-head"><h3>Overview</h3></div>
                <div class="cms-grid">
                    <div class="field"><label>Tab label</label><input name="content[overview][tab]" value="{{ old('content.overview.tab', $content['overview']['tab']) }}"></div>
                    <div class="field"><label>Kicker</label><input name="content[overview][kicker]" value="{{ old('content.overview.kicker', $content['overview']['kicker']) }}"></div>
                    <div class="field full"><label>Heading</label><input name="content[overview][heading]" value="{{ old('content.overview.heading', $content['overview']['heading']) }}"></div>
                    <div class="field full"><label>Body</label><textarea class="short" name="content[overview][body]">{{ old('content.overview.body', $content['overview']['body']) }}</textarea></div>
                </div>
            </section>
            @foreach ($content['overview']['cards'] as $i => $card)
                <section class="card">
                    <div class="card-head"><h3>Overview card {{ $i + 1 }}</h3></div>
                    <div class="field full"><label>Body</label><textarea class="short" name="content[overview][cards][{{ $i }}][body]">{{ old('content.overview.cards.'.$i.'.body', $card['body']) }}</textarea></div>
                </section>
            @endforeach
        @endif
        @if (! empty($content['approach']))
            <section class="card">
                <div class="card-head"><h3>Our approach</h3></div>
                <div class="cms-grid">
                    <div class="field"><label>Tab label</label><input name="content[approach][tab]" value="{{ old('content.approach.tab', $content['approach']['tab']) }}"></div>
                    <div class="field"><label>Kicker</label><input name="content[approach][kicker]" value="{{ old('content.approach.kicker', $content['approach']['kicker']) }}"></div>
                    <div class="field"><label>Heading</label><input name="content[approach][heading]" value="{{ old('content.approach.heading', $content['approach']['heading']) }}"></div>
                    <div class="field"><label>Heading accent</label><input name="content[approach][heading_accent]" value="{{ old('content.approach.heading_accent', $content['approach']['heading_accent']) }}"></div>
                </div>
            </section>
            @foreach ($content['approach']['cards'] as $i => $card)
                <section class="card">
                    <div class="card-head"><h3>Approach card {{ $i + 1 }}</h3></div>
                    <div class="cms-grid">
                        <div class="field full"><label>Title</label><input name="content[approach][cards][{{ $i }}][title]" value="{{ old('content.approach.cards.'.$i.'.title', $card['title']) }}"></div>
                        <div class="field full"><label>Body</label><textarea class="short" name="content[approach][cards][{{ $i }}][body]">{{ old('content.approach.cards.'.$i.'.body', $card['body']) }}</textarea></div>
                    </div>
                </section>
            @endforeach
        @endif
        @if (! empty($content['safety']))
            <section class="card">
                <div class="card-head"><h3>Safety</h3></div>
                <div class="cms-grid">
                    <div class="field"><label>Tab label</label><input name="content[safety][tab]" value="{{ old('content.safety.tab', $content['safety']['tab']) }}"></div>
                    <div class="field"><label>Kicker</label><input name="content[safety][kicker]" value="{{ old('content.safety.kicker', $content['safety']['kicker']) }}"></div>
                    <div class="field full"><label>Heading</label><input name="content[safety][heading]" value="{{ old('content.safety.heading', $content['safety']['heading']) }}"></div>
                    @foreach ($content['safety']['items'] as $i => $item)
                        <div class="field full"><label>Item {{ $i + 1 }}</label><input name="content[safety][items][{{ $i }}]" value="{{ old('content.safety.items.'.$i, $item) }}"></div>
                    @endforeach
                    <div class="field full"><label>Note</label><textarea class="short" name="content[safety][note]">{{ old('content.safety.note', $content['safety']['note']) }}</textarea></div>
                </div>
            </section>
        @endif
    @endif

    @php
        $assetUrl = fn ($path) => $path ? (str_starts_with($path, 'http') ? $path : asset($path)) : '';
    @endphp
    @if (array_key_exists('image', $content['hero'] ?? []) || ! empty($content['inside']))
        <section class="card">
            <div class="card-head"><h3>Images</h3></div>
            <p class="help">Upload a gallery image for this program page. Until you add one, the current video or graphic stays in place.</p>
            <div class="cms-grid-3" style="margin-top:1rem">
                @if (array_key_exists('image', $content['hero'] ?? []))
                    <div class="cms-block">
                        <h4>Gallery image</h4>
                        @if (! empty($content['hero']['image']))
                            <img class="thumb" src="{{ $assetUrl($content['hero']['image']) }}" alt="">
                        @endif
                        <input type="hidden" name="content[hero][image]" value="{{ old('content.hero.image', $content['hero']['image']) }}">
                        <div class="field">
                            <label>Replace image</label>
                            <input type="file" name="uploads[hero]" accept="image/*">
                        </div>
                        <div class="field">
                            <label>Alt text</label>
                            <input name="content[hero][image_alt]" value="{{ old('content.hero.image_alt', $content['hero']['image_alt'] ?? '') }}">
                        </div>
                    </div>
                @endif
                @foreach ($content['inside'] ?? [] as $i => $item)
                    <div class="cms-block">
                        <h4>Gallery {{ $i + 1 }}</h4>
                        @if (! empty($item['image']))
                            <img class="thumb" src="{{ $assetUrl($item['image']) }}" alt="">
                        @endif
                        <input type="hidden" name="content[inside][{{ $i }}][image]" value="{{ old('content.inside.'.$i.'.image', $item['image']) }}">
                        <div class="field">
                            <label>Replace image</label>
                            <input type="file" name="uploads[inside][{{ $i }}]" accept="image/*,.gif">
                        </div>
                        <div class="field">
                            <label>Alt text</label>
                            <input name="content[inside][{{ $i }}][alt]" value="{{ old('content.inside.'.$i.'.alt', $item['alt'] ?? '') }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="form-actions">
        <button type="submit" class="btn-gold">Save page</button>
        <a href="{{ route('superadmin.pages.index') }}" class="btn-ghost">Back to programs</a>
    </div>
</form>

@if ($page->exists)
    <form method="POST" action="{{ route('superadmin.pages.restore', $slug) }}" style="margin-top:1rem" onsubmit="return confirm('Restore the original program copy and discard dashboard edits?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-ghost btn-logout">Restore original copy</button>
    </form>
@endif
@endsection
