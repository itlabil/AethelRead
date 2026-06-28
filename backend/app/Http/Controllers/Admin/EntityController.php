<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Entity\StoreEntityRequest;
use App\Http\Requests\Admin\Entity\UpdateEntityRequest;
use App\Http\Traits\HasTableFilters;
use App\Services\AliasService;
use App\Services\DescriptionService;
use App\Services\EntityService;
use App\Services\ImageService;
use App\Services\KeywordService;
use App\Services\NovelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntityController extends AdminController
{
    use HasTableFilters;

    public function __construct(
        private readonly EntityService      $entityService,
        private readonly NovelService       $novelService,
        private readonly AliasService       $aliasService,
        private readonly KeywordService     $keywordService,
        private readonly DescriptionService $descriptionService,
        private readonly ImageService       $imageService,
    ) {}

    public function index(Request $request): View
    {
        $filters  = $this->getFilterParams($request);
        $entities = $this->entityService->getFilteredPaginated($filters);
        $novels   = $this->novelService->getAllActive();

        return view('admin.entities.index', compact('entities', 'filters', 'novels'));
    }

    public function create(): View
    {
        $novels = $this->novelService->getAllActive();

        return view('admin.entities.create', compact('novels'));
    }

    public function store(StoreEntityRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Aliases & keywords sudah array dari dynamic list
        $data['aliases']  = $request->input('aliases', []);
        $data['keywords'] = $request->input('keywords', []);

        // Parse descriptions
        $data['descriptions'] = array_filter([
            'en' => $request->input('description_en'),
            'id' => $request->input('description_id'),
        ]);

        $entity = $this->entityService->create($data);

        if ($request->hasFile('image')) {
            $this->imageService->upload($entity->id, $request->file('image'));
        }

        return redirect()
            ->route('admin.entities.index')
            ->with('success', 'Entity created successfully.');
    }

    public function show(string $id): View
    {
        $entity = $this->entityService->findById($id);
        $entity->load(['novel', 'aliases', 'keywords', 'descriptions', 'image']);

        return view('admin.entities.show', compact('entity'));
    }

    public function edit(string $id): View
    {
        $entity = $this->entityService->findByIdOrFail($id);
        $entity->load(['aliases', 'keywords', 'descriptions', 'image']);
        $novels = $this->novelService->getAllActive();

        return view('admin.entities.edit', compact('entity', 'novels'));
    }

    public function update(UpdateEntityRequest $request, string $id): RedirectResponse
    {
        $data = $request->validated();

        $data['aliases']  = $request->input('aliases', []);
        $data['keywords'] = $request->input('keywords', []);

        $data['descriptions'] = array_filter([
            'en' => $request->input('description_en'),
            'id' => $request->input('description_id'),
        ]);

        $entity = $this->entityService->update($id, $data);

        if ($request->hasFile('image')) {
            $this->imageService->upload($entity->id, $request->file('image'));
        }

        return redirect()
            ->route('admin.entities.index')
            ->with('success', 'Entity updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->imageService->delete($id);
        $this->entityService->delete($id);

        return redirect()
            ->route('admin.entities.index')
            ->with('success', 'Entity deleted successfully.');
    }

    public function toggle(string $id): RedirectResponse
    {
        $this->entityService->toggleActive($id);

        return back()->with('success', 'Entity status updated.');
    }
}