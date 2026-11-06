<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrainerController extends Controller
{
    public function index(): View
    {
        $trainers = Trainer::query()->ordered()->get();

        return view('superadmin.trainers.index', [
            'trainers' => $trainers,
            'counts' => [
                'all' => $trainers->count(),
                'live' => $trainers->where('is_published', true)->count(),
                'hidden' => $trainers->where('is_published', false)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('superadmin.trainers.form', [
            'trainer' => new Trainer([
                'is_published' => true,
                'sort_order' => (int) Trainer::query()->max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['photo'] = $this->storePhoto($request->file('photo'));

        Trainer::query()->create($data);

        return redirect()
            ->route('superadmin.trainers.index')
            ->with('success', 'Trainer added. They now appear on the Trainers page.');
    }

    public function edit(Trainer $trainer): View
    {
        return view('superadmin.trainers.form', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->file('photo') instanceof UploadedFile) {
            $data['photo'] = $this->storePhoto($request->file('photo'), $trainer->photo);
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($trainer->photo);
            $data['photo'] = null;
        }

        $trainer->update($data);

        return redirect()
            ->route('superadmin.trainers.edit', $trainer)
            ->with('success', 'Trainer saved. Visitors will see the change immediately.');
    }

    public function destroy(Trainer $trainer): RedirectResponse
    {
        $this->deletePhoto($trainer->photo);
        $trainer->delete();

        return redirect()
            ->route('superadmin.trainers.index')
            ->with('success', 'Trainer removed from the website.');
    }

    public function toggle(Trainer $trainer): RedirectResponse
    {
        $trainer->update(['is_published' => ! $trainer->is_published]);

        return redirect()
            ->route('superadmin.trainers.index')
            ->with('success', $trainer->is_published ? 'Trainer is now visible.' : 'Trainer hidden from the website.');
    }

    public function move(Request $request, Trainer $trainer): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ])['direction'];

        $ordered = Trainer::query()->ordered()->get()->values();
        $index = $ordered->search(fn (Trainer $item) => $item->is($trainer));
        $swap = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index !== false && $ordered->has($swap)) {
            $items = $ordered->all();
            [$items[$index], $items[$swap]] = [$items[$swap], $items[$index]];

            DB::transaction(function () use ($items) {
                foreach ($items as $position => $item) {
                    $item->update(['sort_order' => $position + 1]);
                }
            });
        }

        return redirect()->route('superadmin.trainers.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'experience' => ['nullable', 'string', 'max:40'],
            'bio' => ['nullable', 'string', 'max:600'],
            'tags' => ['nullable', 'string', 'max:300'],
            'certification' => ['nullable', 'string', 'max:160'],
            'highlight' => ['nullable', 'string', 'max:160'],
            'languages' => ['nullable', 'string', 'max:160'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'photo' => ImageUpload::rules(),
        ], ImageUpload::messages(['photo']));

        unset($data['photo']);

        $data['tags'] = collect(explode(',', (string) ($data['tags'] ?? '')))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function storePhoto(?UploadedFile $file, ?string $previous = null): ?string
    {
        if (! $file instanceof UploadedFile) {
            return $previous;
        }

        $path = ImageUpload::store($file, 'uploads/trainers', ImageUpload::PHOTO_WIDTH);

        $this->deletePhoto($previous);

        return $path;
    }

    private function deletePhoto(?string $path): void
    {
        if (is_string($path) && str_starts_with($path, 'uploads/trainers/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
