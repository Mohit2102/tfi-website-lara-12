<?php

namespace App\Support;

use App\Services\PlanPricing;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $path = resource_path('html/'.$page.'.html');

        if (! is_file($path)) {
            throw new NotFoundHttpException;
        }

        return response($this->rewrite((string) file_get_contents($path), $page))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function rewrite(string $html, string $page): string
    {
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
            $price = app(PlanPricing::class)->fixedPriceFor($fixedPlanPages[$page]);

            if ($price !== null) {
                $html = preg_replace('/data-price="\d+"/', 'data-price="'.$price.'"', $html, 1) ?? $html;
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

        $html = preg_replace(
            '/<\/body>/i',
            '<script src="/plan-checkout.js"></script></body>',
            $html,
            1
        ) ?? $html;

        return $html;
    }
}
