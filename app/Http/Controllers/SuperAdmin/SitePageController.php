<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use App\Support\HtmlPageRenderer;
use App\Support\PlanPriceCatalog;
use App\Support\ProgramPageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
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

        $name = PlanPriceCatalog::pageTitles()[$slug] ?? $slug;

        if (ProgramPageContent::supports($slug)) {
            return view('superadmin.pages.program', [
                'page' => $page,
                'slug' => $slug,
                'name' => $name,
                'layout' => ProgramPageContent::layout($slug),
                'content' => ProgramPageContent::merge($slug, $page->content),
            ]);
        }

        return view('superadmin.pages.edit', [
            'page' => $page,
            'slug' => $slug,
            'name' => $name,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $this->assertKnownPage($slug);

        $rules = [
            'title' => ['required', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ];

        if (ProgramPageContent::supports($slug)) {
            $rules['content'] = ['required', 'array'];
            $rules['uploads.hero'] = ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp,gif'];
            $rules['uploads.inside'] = ['nullable', 'array'];
            $rules['uploads.inside.*'] = ['nullable', 'file', 'max:8192', 'mimes:jpg,jpeg,png,webp,gif'];
        } else {
            $rules['html'] = ['required', 'string'];
        }

        $data = $request->validate($rules);

        $payload = [
            'title' => $data['title'],
            'meta_description' => $data['meta_description'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ];

        if (ProgramPageContent::supports($slug)) {
            $payload['content'] = $this->storeUploads(
                $request,
                $slug,
                ProgramPageContent::merge($slug, $data['content'] ?? [])
            );
        } else {
            $payload['html'] = $data['html'];
        }

        SitePage::query()->updateOrCreate(['slug' => $slug], $payload);

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

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function storeUploads(Request $request, string $slug, array $content): array
    {
        $hero = $request->file('uploads.hero');

        if ($hero instanceof UploadedFile && $hero->isValid()) {
            $content['hero']['image'] = $this->storePublic($hero, $slug);
        }

        foreach ([0, 1, 2] as $i) {
            $file = $request->file('uploads.inside.'.$i);

            if ($file instanceof UploadedFile && $file->isValid()) {
                $content['inside'][$i]['image'] = $this->storePublic($file, $slug);
            }
        }

        return $content;
    }

    private function storePublic(UploadedFile $file, string $slug): string
    {
        $directory = public_path('uploads/programs/'.$slug);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/programs/'.$slug.'/'.$name;
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
