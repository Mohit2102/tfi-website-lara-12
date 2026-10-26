<?php

namespace App\Support;

use App\Models\SitePage;
use App\Services\PlanPricing;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class HtmlPageRenderer
{
    /** @var list<string> */
    public const PAGES = [
        'index',
        'about',
        'trainers',
        'calculator',
        'payment-success',
        'virtual-training',
        'non-virtual-training',
        'personal-training',
        'monthly-virtual-training',
        '12-weeks-virtual-training',
        'half-yearly-virtual-training',
        'monthly-non-virtual-training',
        '12-weeks-non-virtual-training',
        'half-yearly-non-virtual-training',
        'diabetes-management',
        'pcos-pcod-nutrition',
        'hypertension-heart-healthy',
        'prenatal-postnatal-fitness',
        'back-spine-posture',
        'cardiovascular-conditioning',
        'thyroid-metabolic-support',
        'gerd-support',
    ];

    public function response(string $page): Response
    {
        if (! in_array($page, self::PAGES, true)) {
            throw new NotFoundHttpException;
        }

        $record = $this->record($page);

        if ($record && ! $record->is_published) {
            throw new NotFoundHttpException;
        }

        $html = filled($record?->html) ? (string) $record->html : $this->fileHtml($page);

        if ($html === '') {
            throw new NotFoundHttpException;
        }

        if ($page === 'index') {
            $html = HomePageContent::apply($html, HomePageContent::merge($record?->content));
        }

        return response($this->rewrite($html, $page, $record))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function fileHtml(string $page): string
    {
        $path = resource_path('html/'.$page.'.html');

        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    private function record(string $page): ?SitePage
    {
        try {
            return SitePage::query()->where('slug', $page)->first();
        } catch (Throwable) {
            return null;
        }
    }

    private function rewrite(string $html, string $page, ?SitePage $record): string
    {
        if ($record) {
            if (filled($record->title)) {
                $title = e($record->title);
                $html = preg_replace('/<title>.*?<\/title>/is', '<title>'.$title.'</title>', $html, 1) ?? $html;
            }

            if (filled($record->meta_description)) {
                $meta = e($record->meta_description);
                $replaced = preg_replace(
                    '/(<meta[^>]+name=["\']description["\'][^>]+content=["\'])(.*?)(["\'])/is',
                    '${1}'.$meta.'${3}',
                    $html,
                    1,
                    $count
                );
                if (is_string($replaced) && $count > 0) {
                    $html = $replaced;
                }
            }
        }

        $html = str_replace(
            [
                'href="index.html',
                "href='index.html",
                'SUCCESS_PAGE = "payment-success.html"',
            ],
            [
                'href="/',
                "href='/",
                'SUCCESS_PAGE = "/payment-success"',
            ],
            $html
        );

        foreach (self::PAGES as $pageName) {
            if ($pageName === 'index') {
                continue;
            }

            $html = str_replace(
                ['href="'.$pageName.'.html', "href='".$pageName.'.html'],
                ['href="/'.$pageName, "href='/".$pageName],
                $html
            );
        }

        $fixedPlanPages = [
            'monthly-non-virtual-training' => 'monthly-nv',
            '12-weeks-non-virtual-training' => '12-weeks-nv',
            'half-yearly-non-virtual-training' => 'half-yearly-nv',
        ];

        if (isset($fixedPlanPages[$page])) {
            $pricing = app(PlanPricing::class);
            $price = $pricing->fixedPriceFor($fixedPlanPages[$page]);
            $usd = $pricing->fixedPriceFor($fixedPlanPages[$page], 'usd');

            if ($price !== null) {
                $html = preg_replace('/data-price="[\d.]+"/', 'data-price="'.$price.'"', $html, 1) ?? $html;
            }

            if (is_numeric($usd)) {
                if (preg_match('/data-price-usd="/', $html)) {
                    $html = preg_replace('/data-price-usd="[\d.]+"/', 'data-price-usd="'.$usd.'"', $html, 1) ?? $html;
                } else {
                    $html = preg_replace('/data-price="[\d.]+"/', 'data-price="'.$price.'" data-price-usd="'.$usd.'"', $html, 1) ?? $html;
                }
            }
        }

        if ($page === 'index' && Auth::check()) {
            $user = Auth::user();
            $dashboard = e($user->getDashboardRoute());
            $greeting = 'Hi, '.e($user->name);
            $html = str_replace(
                [
                    '<a class="btn btn-solid nav-book-btn" href="/login">Login/Signup</a>',
                    '<a class="btn btn-solid mobile-book-btn" href="/login">Login/Signup</a>',
                ],
                [
                    '<a class="btn btn-solid nav-book-btn" href="'.$dashboard.'">'.$greeting.'</a>',
                    '<a class="btn btn-solid mobile-book-btn" href="'.$dashboard.'">'.$greeting.'</a>',
                ],
                $html
            );
        }

        $packs = json_encode(
            app(PlanPricing::class)->frontendPacks(),
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
        $geo = app(VisitorCurrency::class)->hint();
        $geoCountry = $geo['country'] ? json_encode($geo['country']) : 'null';
        $geoCurrency = $geo['currency'] ? json_encode($geo['currency']) : 'null';
        $boot = '<script>window.TFI_PLAN_PRICES='.$packs.';window.TFI_GEO_COUNTRY='.$geoCountry.';window.TFI_GEO_CURRENCY='.$geoCurrency.';</script><script src="/tfi-currency.js"></script>';

        $html = preg_replace('/<head[^>]*>/i', '$0'.$boot, $html, 1) ?? $html;

        $html = preg_replace(
            '/<\/body>/i',
            '<script src="/plan-checkout.js"></script></body>',
            $html,
            1
        ) ?? $html;

        return $html;
    }
}
