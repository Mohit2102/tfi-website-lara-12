<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use App\Support\HtmlPageRenderer;
use App\Support\PlanPriceCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitePageController extends Controller
{
    public function index(): View
    {
        $stored = SitePage::query()->get()->keyBy('slug');
        $pages = collect(HtmlPageRenderer::PAGES)->map(function (string $slug) use ($stored) {
            $row = $stored->get($slug);

            return [
                'slug' => $slug,
                'name' => PlanPriceCatalog::pageTitles()[$slug] ?? $slug,
                'title' => $row?->title,
                'is_published' => $row?->is_published ?? true,
                'updated_at' => $row?->updated_at,
                'customized' => $row !== null && (filled($row->html) || filled($row->content)),
            ];
        });

        return view('superadmin.pages.index', compact('pages'));
    }

    public function edit(string $slug): View|RedirectResponse
    {
        $this->assertKnownPage($slug);

        if ($slug === 'index') {
            return redirect()->route('superadmin.home.edit');
        }

        $path = resource_path('html/'.$slug.'.html');
        $fileHtml = is_file($path) ? (string) file_get_contents($path) : '';
        $page = SitePage::query()->firstOrNew(['slug' => $slug], [
            'title' => $this->titleFromHtml($fileHtml) ?: (PlanPriceCatalog::pageTitles()[$slug] ?? $slug),
            'meta_description' => $this->metaFromHtml($fileHtml),
            'html' => $fileHtml,
            'is_published' => true,
        ]);

        if (! filled($page->html)) {
            $page->html = $fileHtml;
        }

        return view('superadmin.pages.edit', [
            'page' => $page,
            'slug' => $slug,
            'name' => PlanPriceCatalog::pageTitles()[$slug] ?? $slug,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $this->assertKnownPage($slug);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'html' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        SitePage::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'html' => $data['html'],
                'is_published' => $request->boolean('is_published'),
            ]
        );

        return redirect()
            ->route('superadmin.pages.edit', $slug)
            ->with('success', 'Page saved. Visitors will see this version immediately.');
    }

    public function restore(string $slug): RedirectResponse
    {
        $this->assertKnownPage($slug);

        SitePage::query()->where('slug', $slug)->delete();

        return redirect()
            ->route('superadmin.pages.edit', $slug)
            ->with('success', 'Restored the original HTML file for this page.');
    }

    private function assertKnownPage(string $slug): void
    {
        abort_unless(in_array($slug, HtmlPageRenderer::PAGES, true), 404);
    }

    private function titleFromHtml(string $html): string
    {
        if (preg_match('/<title>(.*?)<\/title>/is', $html, $match)) {
            return html_entity_decode(trim(strip_tags($match[1])), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function metaFromHtml(string $html): ?string
    {
        if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']/is', $html, $match)) {
            return html_entity_decode(trim($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (preg_match('/<meta[^>]+content=["\'](.*?)["\'][^>]+name=["\']description["\']/is', $html, $match)) {
            return html_entity_decode(trim($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return null;
    }
}
