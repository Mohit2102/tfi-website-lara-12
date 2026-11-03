@props([
    'name' => null,
    'value' => null,
    'src' => null,
    'removeName' => null,
    'variant' => 'thumb',
    'alt' => '',
    'imgStyle' => null,
])
@php
    $url = $src ?: (filled($value) ? (str_starts_with($value, 'http') ? $value : asset($value)) : null);
@endphp
<div {{ $attributes->merge(['class' => 'img-field']) }} data-img-field>
    @if ($name)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-img-value data-original="{{ $value }}">
    @endif

    @if ($url)
        @if ($removeName)
            <input type="hidden" name="{{ $removeName }}" value="0" data-img-remove>
        @endif

        <div class="img-box img-box--{{ $variant }}">
            @if ($variant === 'avatar')
                <div class="avatar avatar-lg"><img src="{{ $url }}" alt="{{ $alt }}"></div>
            @else
                <img class="thumb" src="{{ $url }}" alt="{{ $alt }}" @if ($imgStyle) style="{{ $imgStyle }}" @endif>
            @endif

            <button type="button" class="img-del" data-img-del title="Delete image" aria-label="Delete image">
                <svg class="ic"><use href="#i-trash"/></svg>
            </button>

            <div class="img-removed">
                <span>Removed when you save</span>
                <button type="button" data-img-undo>Undo</button>
            </div>
        </div>
    @endif
</div>
