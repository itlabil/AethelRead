<?php

namespace App\Services;

use App\Models\Description;
use App\Repositories\Contracts\DescriptionRepositoryInterface;
use Illuminate\Support\Collection;

class DescriptionService extends BaseService
{
    public function __construct(
        private readonly DescriptionRepositoryInterface $descriptionRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function getAllByEntity(string $entityId): Collection
    {
        return $this->descriptionRepository->getAllByEntity($entityId);
    }

    public function findByEntityAndLocale(string $entityId, string $locale): ?Description
    {
        return $this->descriptionRepository->findByEntityAndLocale($entityId, $locale);
    }

    public function findByEntityAndLocaleOrFail(string $entityId, string $locale): Description
    {
        $description = $this->descriptionRepository->findByEntityAndLocale($entityId, $locale);

        if (! $description) {
            abort(404, "Description not found for locale '{$locale}'.");
        }

        return $description;
    }

    /*
    |--------------------------------------------------------------------------
    | Write Operations
    |--------------------------------------------------------------------------
    */

    public function upsert(string $entityId, string $locale, string $content): Description
    {
        $this->validateLocale($locale);

        return $this->descriptionRepository->upsert(
            $entityId,
            $locale,
            $this->sanitize($content)
        );
    }

    public function upsertMany(string $entityId, array $descriptions): Collection
    {
        $results = collect();

        foreach ($descriptions as $locale => $content) {
            $results->push(
                $this->upsert($entityId, $locale, $content)
            );
        }

        return $results;
    }

    public function delete(string $id): bool
    {
        return $this->descriptionRepository->delete($id);
    }

    public function deleteAllByEntity(string $entityId): bool
    {
        return $this->descriptionRepository->deleteAllByEntity($entityId);
    }

    /*
    |--------------------------------------------------------------------------
    | Sync Operations
    |--------------------------------------------------------------------------
    */

    public function sync(string $entityId, array $descriptions): Collection
    {
        $this->descriptionRepository->deleteAllByEntity($entityId);

        if (empty($descriptions)) {
            return collect();
        }

        return $this->upsertMany($entityId, $descriptions);
    }

    /*
    |--------------------------------------------------------------------------
    | Locale Helpers
    |--------------------------------------------------------------------------
    */

    public function getSupportedLocales(): array
    {
        return ['en', 'id'];
    }

    public function isValidLocale(string $locale): bool
    {
        return in_array($locale, $this->getSupportedLocales());
    }

    public function validateLocale(string $locale): void
    {
        if (! $this->isValidLocale($locale)) {
            abort(422, "Unsupported locale '{$locale}'. Supported: " . implode(', ', $this->getSupportedLocales()));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Content Helpers
    |--------------------------------------------------------------------------
    */

    public function sanitize(string $content): string
    {
        return trim($content);
    }

    public function getLocaleLabel(string $locale): string
    {
        return match ($locale) {
            'en'    => 'English',
            'id'    => 'Indonesian',
            default => strtoupper($locale),
        };
    }
}