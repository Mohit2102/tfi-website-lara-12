@extends('layouts.superadmin')

@section('title', 'Home page')
@section('heading', 'Home page')
@section('subheading', 'This is the public homepage. Saved fields go live immediately.')

@section('content')
@php
    $assetUrl = fn ($path) => $path ? (str_starts_with($path, 'http') ? $path : asset($path)) : '';
@endphp
<form method="POST" action="{{ route('superadmin.home.update') }}" enctype="multipart/form-data" class="cms-stack">
    @csrf
    @method('PUT')

    <section class="card">
        <div class="cms-toolbar">
            <p class="help">Edit the homepage copy, stats, images, founder and testimonials. Upload a new image or keep the current path.</p>
            <a class="btn-ghost" href="{{ url('/') }}" target="_blank" rel="noopener">View live <svg class="ic"><use href="#i-up-right"/></svg></a>
        </div>
        <div class="cms-grid" style="margin-top:1.2rem">
            <div class="field">
                <label for="title">Browser title</label>
                <input id="title" name="title" value="{{ old('title', $page->title) }}" required>
            </div>
            <div class="field">
                <label for="meta_description">Meta description</label>
                <input id="meta_description" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}">
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Hero slides</h3></div>
        <div class="cms-grid-3">
            @foreach ($content['hero'] as $i => $slide)
                <div class="cms-block">
                    <h4>Slide {{ $i + 1 }}</h4>
                    @if ($slide['image'])
                        <img class="thumb" src="{{ $assetUrl($slide['image']) }}" alt="">
                    @endif
                    {{-- Image path
                    <div class="field">
                        <label>Image path</label>
                        <input name="content[hero][{{ $i }}][image]" value="{{ old('content.hero.'.$i.'.image', $slide['image']) }}">
                    </div>
                    --}}
                    <input type="hidden" name="content[hero][{{ $i }}][image]" value="{{ old('content.hero.'.$i.'.image', $slide['image']) }}">
                    <div class="field">
                        <label>Replace image</label>
                        <input type="file" name="uploads[hero][{{ $i }}]" accept="image/*">
                    </div>
                    <div class="field">
                        <label>Alt text</label>
                        <input name="content[hero][{{ $i }}][alt]" value="{{ old('content.hero.'.$i.'.alt', $slide['alt']) }}">
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Stats bar</h3></div>
        <div class="cms-grid">
            @foreach ($content['stats'] as $i => $stat)
                <div class="cms-block">
                    <div class="cms-grid">
                        <div class="field">
                            <label>Number</label>
                            <input name="content[stats][{{ $i }}][value]" value="{{ old('content.stats.'.$i.'.value', $stat['value']) }}">
                        </div>
                        <div class="field">
                            <label>Suffix</label>
                            <input name="content[stats][{{ $i }}][suffix]" value="{{ old('content.stats.'.$i.'.suffix', $stat['suffix']) }}" placeholder="+">
                        </div>
                    </div>
                    <div class="field">
                        <label>Label</label>
                        <input name="content[stats][{{ $i }}][label]" value="{{ old('content.stats.'.$i.'.label', $stat['label']) }}">
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Intro</h3></div>
        <div class="cms-grid">
            <div class="field full">
                <label>Eyebrow</label>
                <input name="content[intro][eyebrow]" value="{{ old('content.intro.eyebrow', $content['intro']['eyebrow']) }}">
            </div>
            <div class="field">
                <label>Heading</label>
                <input name="content[intro][heading]" value="{{ old('content.intro.heading', $content['intro']['heading']) }}">
            </div>
            <div class="field">
                <label>Heading accent</label>
                <input name="content[intro][heading_accent]" value="{{ old('content.intro.heading_accent', $content['intro']['heading_accent']) }}">
            </div>
            <div class="field full">
                <label>Subtitle</label>
                <input name="content[intro][subtitle]" value="{{ old('content.intro.subtitle', $content['intro']['subtitle']) }}">
            </div>
            <div class="field full">
                <label>Body</label>
                <textarea class="short" name="content[intro][body]">{{ old('content.intro.body', $content['intro']['body']) }}</textarea>
            </div>
        </div>
        <div class="cms-grid-3" style="margin-top:1rem">
            @foreach ($content['intro']['steps'] as $i => $step)
                <div class="cms-block">
                    <h4>Step {{ $i + 1 }}</h4>
                    <div class="field">
                        <label>Title</label>
                        <input name="content[intro][steps][{{ $i }}][title]" value="{{ old('content.intro.steps.'.$i.'.title', $step['title']) }}">
                    </div>
                    <div class="field">
                        <label>Text</label>
                        <textarea class="short" name="content[intro][steps][{{ $i }}][text]">{{ old('content.intro.steps.'.$i.'.text', $step['text']) }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Fitness journey</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Kicker</label>
                <input name="content[journey][kicker]" value="{{ old('content.journey.kicker', $content['journey']['kicker']) }}">
            </div>
            <div class="field">
                <label>Heading</label>
                <textarea class="short" name="content[journey][heading]">{{ old('content.journey.heading', $content['journey']['heading']) }}</textarea>
            </div>
            <div class="field full">
                <label>Body</label>
                <textarea class="short" name="content[journey][body]">{{ old('content.journey.body', $content['journey']['body']) }}</textarea>
            </div>
            <div class="field full">
                <label>Footer line</label>
                <input name="content[journey][footer]" value="{{ old('content.journey.footer', $content['journey']['footer']) }}">
            </div>
        </div>
        @foreach ($content['journey']['steps'] as $i => $step)
            <div class="cms-block" style="margin-top:1rem">
                <h4>Timeline step {{ $i + 1 }}</h4>
                <div class="cms-grid">
                    <div class="field">
                        <label>Number</label>
                        <input name="content[journey][steps][{{ $i }}][number]" value="{{ old('content.journey.steps.'.$i.'.number', $step['number']) }}">
                    </div>
                    <div class="field">
                        <label>Label</label>
                        <input name="content[journey][steps][{{ $i }}][label]" value="{{ old('content.journey.steps.'.$i.'.label', $step['label']) }}">
                    </div>
                    <div class="field">
                        <label>Title</label>
                        <input name="content[journey][steps][{{ $i }}][title]" value="{{ old('content.journey.steps.'.$i.'.title', $step['title']) }}">
                    </div>
                    <div class="field full">
                        <label>Description</label>
                        <textarea class="short" name="content[journey][steps][{{ $i }}][description]">{{ old('content.journey.steps.'.$i.'.description', $step['description']) }}</textarea>
                    </div>
                    @foreach ($step['points'] as $p => $point)
                        <div class="field">
                            <label>Point {{ $p + 1 }}</label>
                            <input name="content[journey][steps][{{ $i }}][points][{{ $p }}]" value="{{ old('content.journey.steps.'.$i.'.points.'.$p, $point) }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <section class="card">
        <div class="card-head"><h3>Programs</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Kicker</label>
                <input name="content[programs][kicker]" value="{{ old('content.programs.kicker', $content['programs']['kicker']) }}">
            </div>
            <div class="field">
                <label>Heading</label>
                <input name="content[programs][heading]" value="{{ old('content.programs.heading', $content['programs']['heading']) }}">
            </div>
            <div class="field full">
                <label>Body</label>
                <textarea class="short" name="content[programs][body]">{{ old('content.programs.body', $content['programs']['body']) }}</textarea>
            </div>
        </div>
        <div class="cms-grid-3" style="margin-top:1rem">
            @foreach ($content['programs']['cards'] as $i => $card)
                <div class="cms-block">
                    <h4>Card {{ $i + 1 }}</h4>
                    @if ($card['image'])
                        <img class="thumb" src="{{ $assetUrl($card['image']) }}" alt="">
                    @endif
                    <div class="field">
                        <label>Title</label>
                        <input name="content[programs][cards][{{ $i }}][title]" value="{{ old('content.programs.cards.'.$i.'.title', $card['title']) }}">
                    </div>
                    <div class="field">
                        <label>Body</label>
                        <textarea class="short" name="content[programs][cards][{{ $i }}][body]">{{ old('content.programs.cards.'.$i.'.body', $card['body']) }}</textarea>
                    </div>
                    <div class="field">
                        <label>Button text</label>
                        <input name="content[programs][cards][{{ $i }}][cta]" value="{{ old('content.programs.cards.'.$i.'.cta', $card['cta']) }}">
                    </div>
                    {{-- Image path
                    <div class="field">
                        <label>Image path</label>
                        <input name="content[programs][cards][{{ $i }}][image]" value="{{ old('content.programs.cards.'.$i.'.image', $card['image']) }}">
                    </div>
                    --}}
                    <input type="hidden" name="content[programs][cards][{{ $i }}][image]" value="{{ old('content.programs.cards.'.$i.'.image', $card['image']) }}">
                    <div class="field">
                        <label>Replace image</label>
                        <input type="file" name="uploads[programs][{{ $i }}]" accept="image/*">
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Why TFI</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Kicker</label>
                <input name="content[why][kicker]" value="{{ old('content.why.kicker', $content['why']['kicker']) }}">
            </div>
            <div class="field">
                <label>Heading</label>
                <input name="content[why][heading]" value="{{ old('content.why.heading', $content['why']['heading']) }}">
            </div>
            <div class="field">
                <label>Heading accent</label>
                <input name="content[why][heading_accent]" value="{{ old('content.why.heading_accent', $content['why']['heading_accent']) }}">
            </div>
            <div class="field">
                <label>Subtitle</label>
                <input name="content[why][subtitle]" value="{{ old('content.why.subtitle', $content['why']['subtitle']) }}">
            </div>
        </div>
        <div class="cms-stack" style="margin-top:1rem">
            @foreach ($content['why']['cards'] as $i => $card)
                <div class="cms-block">
                    <h4>Why card {{ $i + 1 }}</h4>
                    <div class="cms-grid">
                        <div class="field">
                            <label>Title</label>
                            <textarea class="short" name="content[why][cards][{{ $i }}][title]">{{ old('content.why.cards.'.$i.'.title', $card['title']) }}</textarea>
                        </div>
                        <div class="field">
                            <label>Body</label>
                            <textarea class="short" name="content[why][cards][{{ $i }}][body]">{{ old('content.why.cards.'.$i.'.body', $card['body']) }}</textarea>
                        </div>
                        {{-- Image path
                        <div class="field">
                            <label>Image path</label>
                            <input name="content[why][cards][{{ $i }}][image]" value="{{ old('content.why.cards.'.$i.'.image', $card['image']) }}">
                        </div>
                        --}}
                        <input type="hidden" name="content[why][cards][{{ $i }}][image]" value="{{ old('content.why.cards.'.$i.'.image', $card['image']) }}">
                        <div class="field">
                            <label>Replace image</label>
                            <input type="file" name="uploads[why][{{ $i }}]" accept="image/*">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="cms-grid" style="margin-top:1rem">
            <div class="field">
                <label>Team heading</label>
                <textarea class="short" name="content[why][team_heading]">{{ old('content.why.team_heading', $content['why']['team_heading']) }}</textarea>
            </div>
            <div class="field">
                <label>Team body</label>
                <textarea class="short" name="content[why][team_body]">{{ old('content.why.team_body', $content['why']['team_body']) }}</textarea>
            </div>
            {{-- Image path
            <div class="field">
                <label>Team image</label>
                <input name="content[why][team_image]" value="{{ old('content.why.team_image', $content['why']['team_image']) }}">
            </div>
            --}}
            <input type="hidden" name="content[why][team_image]" value="{{ old('content.why.team_image', $content['why']['team_image']) }}">
            <div class="field">
                <label>Replace team image</label>
                <input type="file" name="uploads[why_team]" accept="image/*">
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Founder</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Kicker</label>
                <input name="content[founder][kicker]" value="{{ old('content.founder.kicker', $content['founder']['kicker']) }}">
            </div>
            <div class="field">
                <label>Name</label>
                <input name="content[founder][name]" value="{{ old('content.founder.name', $content['founder']['name']) }}">
            </div>
            <div class="field">
                <label>Role</label>
                <input name="content[founder][role]" value="{{ old('content.founder.role', $content['founder']['role']) }}">
            </div>
            <div class="field">
                <label>Caption</label>
                <input name="content[founder][caption]" value="{{ old('content.founder.caption', $content['founder']['caption']) }}">
            </div>
            <div class="field">
                <label>Experience number</label>
                <input name="content[founder][experience]" value="{{ old('content.founder.experience', $content['founder']['experience']) }}">
            </div>
            <div class="field">
                <label>Experience label</label>
                <textarea class="short" name="content[founder][experience_label]">{{ old('content.founder.experience_label', $content['founder']['experience_label']) }}</textarea>
            </div>
            <div class="field full">
                <label>Lead</label>
                <textarea class="short" name="content[founder][lead]">{{ old('content.founder.lead', $content['founder']['lead']) }}</textarea>
            </div>
            <div class="field full">
                <label>Bio</label>
                <textarea class="short" name="content[founder][bio]">{{ old('content.founder.bio', $content['founder']['bio']) }}</textarea>
            </div>
            <div class="field full">
                <label>Quote</label>
                <textarea class="short" name="content[founder][quote]">{{ old('content.founder.quote', $content['founder']['quote']) }}</textarea>
            </div>
            {{-- Image path
            <div class="field">
                <label>Photo path</label>
                <input name="content[founder][image]" value="{{ old('content.founder.image', $content['founder']['image']) }}">
            </div>
            --}}
            <input type="hidden" name="content[founder][image]" value="{{ old('content.founder.image', $content['founder']['image']) }}">
            <div class="field">
                <label>Replace photo</label>
                <input type="file" name="uploads[founder]" accept="image/*">
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Testimonials</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Kicker</label>
                <input name="content[testimonials][kicker]" value="{{ old('content.testimonials.kicker', $content['testimonials']['kicker']) }}">
            </div>
            <div class="field">
                <label>Heading</label>
                <input name="content[testimonials][heading]" value="{{ old('content.testimonials.heading', $content['testimonials']['heading']) }}">
            </div>
        </div>
        <div class="cms-grid" style="margin-top:1rem">
            @foreach ($content['testimonials']['items'] as $i => $item)
                <div class="cms-block">
                    <h4>Quote {{ $i + 1 }}</h4>
                    <div class="field">
                        <label>Quote</label>
                        <textarea class="short" name="content[testimonials][items][{{ $i }}][quote]">{{ old('content.testimonials.items.'.$i.'.quote', $item['quote']) }}</textarea>
                    </div>
                    <div class="field">
                        <label>Name</label>
                        <input name="content[testimonials][items][{{ $i }}][name]" value="{{ old('content.testimonials.items.'.$i.'.name', $item['name']) }}">
                    </div>
                    <div class="field">
                        <label>Program</label>
                        <input name="content[testimonials][items][{{ $i }}][program]" value="{{ old('content.testimonials.items.'.$i.'.program', $item['program']) }}">
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="card">
        <div class="card-head"><h3>Who we train &amp; contact</h3></div>
        <div class="cms-grid">
            <div class="field">
                <label>Who kicker</label>
                <input name="content[who][kicker]" value="{{ old('content.who.kicker', $content['who']['kicker']) }}">
            </div>
            <div class="field">
                <label>Who heading</label>
                <textarea class="short" name="content[who][heading]">{{ old('content.who.heading', $content['who']['heading']) }}</textarea>
            </div>
            <div class="field full">
                <label>Who subtitle</label>
                <input name="content[who][subtitle]" value="{{ old('content.who.subtitle', $content['who']['subtitle']) }}">
            </div>
            <div class="field">
                <label>Men intro</label>
                <textarea class="short" name="content[who][men]">{{ old('content.who.men', $content['who']['men']) }}</textarea>
            </div>
            <div class="field">
                <label>Women intro</label>
                <textarea class="short" name="content[who][women]">{{ old('content.who.women', $content['who']['women']) }}</textarea>
            </div>
            <div class="field">
                <label>Focus heading</label>
                <textarea class="short" name="content[who][focus_heading]">{{ old('content.who.focus_heading', $content['who']['focus_heading']) }}</textarea>
            </div>
            <div class="field">
                <label>Focus body</label>
                <textarea class="short" name="content[who][focus_body]">{{ old('content.who.focus_body', $content['who']['focus_body']) }}</textarea>
            </div>
            <div class="field">
                <label>BMI kicker</label>
                <input name="content[bmi][kicker]" value="{{ old('content.bmi.kicker', $content['bmi']['kicker']) }}">
            </div>
            <div class="field">
                <label>BMI heading</label>
                <input name="content[bmi][heading]" value="{{ old('content.bmi.heading', $content['bmi']['heading']) }}">
            </div>
            <div class="field">
                <label>BMI accent</label>
                <input name="content[bmi][heading_accent]" value="{{ old('content.bmi.heading_accent', $content['bmi']['heading_accent']) }}">
            </div>
            {{-- Image path
            <div class="field">
                <label>BMI image</label>
                <input name="content[bmi][image]" value="{{ old('content.bmi.image', $content['bmi']['image']) }}">
            </div>
            --}}
            <input type="hidden" name="content[bmi][image]" value="{{ old('content.bmi.image', $content['bmi']['image']) }}">
            <div class="field">
                <label>Replace BMI image</label>
                <input type="file" name="uploads[bmi]" accept="image/*">
            </div>
            <div class="field">
                <label>Contact heading</label>
                <input name="content[contact][heading]" value="{{ old('content.contact.heading', $content['contact']['heading']) }}">
            </div>
            <div class="field">
                <label>Contact flag</label>
                <input name="content[contact][flag]" value="{{ old('content.contact.flag', $content['contact']['flag']) }}">
            </div>
            <div class="field full">
                <label>Contact body</label>
                <textarea class="short" name="content[contact][body]">{{ old('content.contact.body', $content['contact']['body']) }}</textarea>
            </div>
        </div>
    </section>

    <div class="form-actions">
        <button type="submit" class="btn-gold">Save home page</button>
        <a href="{{ url('/') }}" class="btn-ghost">Preview website</a>
    </div>
</form>
@endsection
