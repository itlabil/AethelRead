<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Novel\StoreNovelRequest;
use App\Http\Requests\Admin\Novel\UpdateNovelRequest;
use App\Http\Traits\HasTableFilters;
use App\Services\HashService;
use App\Services\NovelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NovelController extends AdminController
{
    use HasTableFilters;

    public function __construct(
        private readonly NovelService $novelService,
        private readonly HashService  $hashService,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->getFilterParams($request);
        $novels  = $this->novelService->getFilteredPaginated($filters);

        return view('admin.novels.index', compact('novels', 'filters'));
    }

    public function create(): View
    {
        return view('admin.novels.create');
    }

    public function store(StoreNovelRequest $request): RedirectResponse
    {
        // Pisahkan field cover dari data novel
        $data = collect($request->validated())
            ->except(['cover'])
            ->toArray();

        $novel = $this->novelService->create($data);

        if ($request->hasFile('cover')) {
            $this->novelService->uploadCover($novel->id, $request->file('cover'));
        }

        return redirect()
            ->route('admin.novels.index')
            ->with('success', 'Novel created successfully.');
    }

    public function edit(string $id): View
    {
        $novel = $this->novelService->findByIdOrFail($id);

        return view('admin.novels.edit', compact('novel'));
    }

    public function update(UpdateNovelRequest $request, string $id): RedirectResponse
    {
        // Pisahkan field cover dari data novel
        $data = collect($request->validated())
            ->except(['cover'])
            ->toArray();

        $this->novelService->update($id, $data);

        if ($request->hasFile('cover')) {
            $this->novelService->uploadCover($id, $request->file('cover'));
        }

        return redirect()
            ->route('admin.novels.index')
            ->with('success', 'Novel updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->novelService->deleteCover($id);
        $this->novelService->delete($id);

        return redirect()
            ->route('admin.novels.index')
            ->with('success', 'Novel deleted successfully.');
    }

    public function toggle(string $id): RedirectResponse
    {
        $this->novelService->toggleActive($id);

        return back()->with('success', 'Novel status updated.');
    }

    public function destroyCover(string $id): RedirectResponse
    {
        $this->novelService->deleteCover($id);

        return back()->with('success', 'Cover deleted successfully.');
    }

    // Entity management via Novel
    public function entities(Request $request, string $id): View
    {
        $novel   = $this->novelService->findByIdOrFail($id);
        $filters = $this->getFilterParams($request);
        $filters['novel_id'] = $novel->id;

        $entityService = app(\App\Services\EntityService::class);
        $entities      = $entityService->getFilteredPaginated($filters);

        // Eager load image untuk menghindari lazy loading violation
        $entities->load('image');

        return view('admin.novels.entities', compact('novel', 'entities', 'filters'));
    }
}