<?php

namespace App\Support;

use App\Models\Trainer;
use Illuminate\Support\Collection;
use Throwable;

class TrainersPageContent
{
    public const START = '<!-- trainers:start -->';

    public const END = '<!-- trainers:end -->';

    /** @var list<string> */
    private const FALLBACKS = [
        'linear-gradient(135deg, #c99a2e, #3a2a08)',
        'linear-gradient(135deg, #e2bd5b, #5a3d0a)',
        'linear-gradient(135deg, #a8791c, #1f1608)',
        'linear-gradient(135deg, #d4a83a, #2e2209)',
        'linear-gradient(135deg, #f3d98b, #6b4a0e)',
        'linear-gradient(135deg, #8f6819, #17110a)',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'kicker' => 'Your Goals. Our Expertise.',
                'title' => 'Meet Our',
                'title_accent' => 'Trainers',
                'body' => "Qualified people. Professional coaching. Explore each coach's certifications, experience and specialization to find the right fit for your fitness journey.",
            ],
            'stats' => [
                'coaches_label' => 'Expert Coaches',
                'experience_label' => 'Years Combined Experience',
                'clients_value' => '1500+',
                'clients_label' => 'Clients Trained',
            ],
            'cards' => [
                'button' => 'Book Now',
                'empty' => 'Our coaching team will be introduced here soon.',
            ],
            'cta' => [
                'heading' => 'Not sure which',
                'heading_accent' => 'trainer',
                'heading_tail' => 'is right for you?',
                'body' => 'Tell us your goal and our team will match you with the perfect coach.',
                'button' => 'Get Matched →',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $saved
     * @return array<string, mixed>
     */
    public static function merge(?array $saved): array
    {
        return array_replace_recursive(self::defaults(), $saved ?? []);
    }

    /**
     * @param  array<string, mixed>|null  $saved
     */
    public static function apply(string $html, ?array $saved = null): string
    {
        $content = self::merge($saved);
        $html = CmsMarkup::applyChanged($html, self::defaults(), $content);
        $trainers = self::published();

        if ($trainers === null) {
            return $html;
        }

        $start = strpos($html, self::START);
        $end = strpos($html, self::END);

        if ($start !== false && $end !== false && $end > $start) {
            $html = substr($html, 0, $start + strlen(self::START))
                ."\n".self::cards($trainers, $content['cards'])."\n"
                .substr($html, $end);
        }

        $html = preg_replace(
            '/(data-cms="trainers\.count"[^>]*>)[^<]*/',
            '${1}'.$trainers->count(),
            $html
        ) ?? $html;

        $years = self::combinedYears($trainers);

        if ($years === 0) {
            return $html;
        }

        return preg_replace(
            '/(data-cms="trainers\.experience"[^>]*>)[^<]*/',
            '${1}'.$years.'+',
            $html
        ) ?? $html;
    }

    /**
     * @param  Collection<int, Trainer>  $trainers
     */
    private static function combinedYears(Collection $trainers): int
    {
        return (int) $trainers->sum(
            fn (Trainer $trainer) => preg_match('/\d+/', (string) $trainer->experience, $match) ? (int) $match[0] : 0
        );
    }

    /**
     * @return Collection<int, Trainer>|null
     */
    private static function published(): ?Collection
    {
        try {
            return Trainer::query()->published()->ordered()->get();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param  Collection<int, Trainer>  $trainers
     * @param  array<string, mixed>  $copy
     */
    private static function cards(Collection $trainers, array $copy): string
    {
        if ($trainers->isEmpty()) {
            return '<p class="tr-empty">'.e((string) $copy['empty']).'</p>';
        }

        return $trainers->values()
            ->map(fn (Trainer $trainer, int $i) => self::card($trainer, $i, (string) $copy['button']))
            ->implode("\n");
    }

    private static function card(Trainer $trainer, int $index, string $button): string
    {
        $fallback = self::FALLBACKS[$index % count(self::FALLBACKS)];
        $photo = $trainer->photoUrl();
        $background = $photo
            ? "url('".e($photo)."'), ".$fallback
            : $fallback;

        $exp = filled($trainer->experience)
            ? '<span class="tr-exp"><i class="fa-solid fa-medal"></i>'.e(mb_strtoupper($trainer->experience)).'</span>'
            : '';

        $bio = filled($trainer->bio)
            ? '<p class="tr-bio">'.e($trainer->bio).'</p>'
            : '';

        $tags = collect($trainer->tags ?? [])->filter(fn ($tag) => filled($tag));
        $tagsHtml = $tags->isNotEmpty()
            ? '<div class="tr-tags">'.$tags->map(fn ($tag) => '<span>'.e($tag).'</span>')->implode('').'</div>'
            : '';

        $meta = collect([
            ['fa-certificate', $trainer->certification],
            ['fa-user-group', $trainer->highlight],
            ['fa-language', $trainer->languages],
        ])->filter(fn (array $item) => filled($item[1]));

        $metaHtml = $meta->isNotEmpty()
            ? '<ul class="tr-meta">'.$meta->map(
                fn (array $item) => '<li><i class="fa-solid '.$item[0].'"></i>'.e($item[1]).'</li>'
            )->implode('').'</ul>'
            : '';

        $socials = collect([
            ['Instagram', 'fa-instagram', $trainer->instagram_url],
            ['LinkedIn', 'fa-linkedin-in', $trainer->linkedin_url],
        ])->filter(fn (array $item) => filled($item[2]));

        $socialsHtml = '<div class="tr-socials">'.$socials->map(
            fn (array $item) => '<a href="'.e($item[2]).'" target="_blank" rel="noopener" aria-label="'.$item[0].'"><i class="fa-brands '.$item[1].'"></i></a>'
        )->implode('').'</div>';

        return '<article class="tr-card">'
            .'<div class="tr-photo'.($photo ? ' has-photo' : '').'" style="background-image:'.$background.'">'
            .$exp
            .'<div class="tr-photo-name"><h3>'.e($trainer->name).'</h3></div>'
            .'</div>'
            .'<div class="tr-body">'
            .$bio
            .$tagsHtml
            .$metaHtml
            .'<div class="tr-foot">'
            .$socialsHtml
            .'<a href="index.html#contact" class="btn btn-solid tr-book">'.e($button).' <span>→</span></a>'
            .'</div>'
            .'</div>'
            .'</article>';
    }
}
