<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use App\Support\HomePageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomePageController extends Controller
{
    public function edit(): View
    {
        $page = SitePage::query()->firstOrNew(['slug' => 'index'], [
            'title' => 'TFI — Time To Fit India',
            'meta_description' => 'TFI — Time To Fit India. Personalised virtual and in-person personal training, workout and nutrition plans.',
            'is_published' => true,
        ]);

        $content = HomePageContent::merge($page->content);

        return view('superadmin.home.edit', compact('page', 'content'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'array'],
        ]);

        $content = HomePageContent::merge($data['content']);
        $content = $this->storeUploads($request, $content);

        SitePage::query()->updateOrCreate(
            ['slug' => 'index'],
            [
                'title' => $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'content' => $content,
                'is_published' => true,
            ]
        );

        return redirect()
            ->route('superadmin.home.edit')
            ->with('success', 'Home page saved. Visitors will see the new content immediately.');
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function storeUploads(Request $request, array $content): array
    {
        $map = [
            'hero.0' => ['hero', 0, 'image'],
            'hero.1' => ['hero', 1, 'image'],
            'hero.2' => ['hero', 2, 'image'],
            'programs.0' => ['programs', 'cards', 0, 'image'],
            'programs.1' => ['programs', 'cards', 1, 'image'],
            'programs.2' => ['programs', 'cards', 2, 'image'],
            'bmi' => ['bmi', 'image'],
            'founder' => ['founder', 'image'],
            'why_team' => ['why', 'team_image'],
        ];

        foreach ($map as $input => $path) {
            $file = $request->file('uploads.'.$input);

            if ($file instanceof UploadedFile && $file->isValid()) {
                $this->putPath($content, $path, $this->storePublic($file));
            }
        }

        foreach ([0, 1, 2, 3, 4] as $i) {
            $file = $request->file('uploads.why.'.$i);

            if ($file instanceof UploadedFile && $file->isValid()) {
                $content['why']['cards'][$i]['image'] = $this->storePublic($file);
            }
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  list<int|string>  $path
     */
    private function putPath(array &$content, array $path, string $value): void
    {
        $ref = &$content;

        foreach ($path as $segment) {
            $ref = &$ref[$segment];
        }

        $ref = $value;
    }

    private function storePublic(UploadedFile $file): string
    {
        $directory = public_path('uploads/home');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $name);

        return 'uploads/home/'.$name;
    }
}
