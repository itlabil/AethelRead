<?php

namespace App\Services;

use App\Models\Novel;
use App\Repositories\Contracts\NovelRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NovelService extends BaseService
{
    public function __construct(
        private readonly NovelRepositoryInterface $novelRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAll(): Collection
    {
        return $this->novelRepository->all();
    }

    public function getAllActive(): Collection
    {
        return $this->novelRepository->getAllActive();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->novelRepository->paginate($perPage);
    }

    public function getPaginatedActive(int $perPage = 15): LengthAwarePaginator
    {
        return $this->novelRepository->getPaginatedActive($perPage);
    }

    public function findById(string $id): ?Novel
    {
        return $this->novelRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Novel
    {
        return $this->novelRepository->findBySlug($slug);
    }

    public function findBySlugOrFail(string $slug): Novel
    {
        return $this->novelRepository->findBySlugOrFail($slug);
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Novel
    {
        $data['hash'] = $this->generateHash($data);

        return $this->novelRepository->create($data);
    }

    public function update(string $id, array $data): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        // Regenerate hash on update
        $data['hash'] = $this->generateHash(array_merge($novel->toArray(), $data));

        return $this->novelRepository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->novelRepository->delete($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Operations
    |--------------------------------------------------------------------------
    */

    public function toggleActive(string $id): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        return $this->novelRepository->update($id, [
            'is_active' => ! $novel->is_active,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hash Operations
    |--------------------------------------------------------------------------
    */

    public function refreshHash(string $id): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);
        $hash  = $this->generateHash($novel->toArray());

        return $this->novelRepository->updateHash($id, $hash);
    }

    public function verifyHash(string $slug, string $clientHash): bool
    {
        $novel = $this->novelRepository->findBySlug($slug);

        if (! $novel) {
            return false;
        }

        return $novel->hash === $clientHash;
    }

    /*
    |--------------------------------------------------------------------------
    | Find By Id Or Fail
    |--------------------------------------------------------------------------
    */

    public function findByIdOrFail(string $id): Novel
    {
        return $this->novelRepository->findByIdOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Filtered Paginated
    |--------------------------------------------------------------------------
    */

    public function getFilteredPaginated(array $filters): LengthAwarePaginator
    {
        $query = $this->novelRepository->query();

        // Search
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'ilike', "%{$filters['search']}%")
                ->orWhere('slug', 'ilike', "%{$filters['search']}%");
            });
        }

        // Filter by type
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filter by status
        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        // Sort
        $query->orderBy($filters['sort'], $filters['direction']);

        return $query->paginate($filters['per_page'])->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | Image Prosessing Operations
    |--------------------------------------------------------------------------
    */

    public function uploadCover(string $id, UploadedFile $file): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        // Delete existing cover
        if ($novel->cover_path) {
            Storage::disk('public')->delete($novel->cover_path);
            Storage::disk('public')->delete($novel->cover_thumbnail_path);
        }

        // Store original
        $originalPath = $file->store('novels/covers/original', 'public');

        // Process thumbnail
        $filename  = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        $thumbPath = 'novels/covers/thumbnails/' . $filename;
        $fullPath  = Storage::disk('public')->path($thumbPath);

        Storage::disk('public')->makeDirectory('novels/covers/thumbnails');

        $manager = new ImageManager(new Driver());
        $image   = $manager->read($file->getPathname())
            ->cover(300, 420)
            ->toWebp(quality: 85);

        file_put_contents($fullPath, $image);

        return $this->novelRepository->update($id, [
            'cover_path'           => $originalPath,
            'cover_thumbnail_path' => $thumbPath,
        ]);
    }

    public function deleteCover(string $id): Novel
    {
        $novel = $this->novelRepository->findByIdOrFail($id);

        if ($novel->cover_path) {
            Storage::disk('public')->delete($novel->cover_path);
            Storage::disk('public')->delete($novel->cover_thumbnail_path);
        }

        return $this->novelRepository->update($id, [
            'cover_path'           => null,
            'cover_thumbnail_path' => null,
        ]);
    }
}