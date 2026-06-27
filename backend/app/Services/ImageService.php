<?php

namespace App\Services;

use App\Models\Image;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image as ImageProcessor;

class ImageService extends BaseService
{
    /**
     * Thumbnail dimensions (square).
     */
    private const THUMBNAIL_SIZE = 512;

    /**
     * Storage disk.
     */
    private const DISK = 'public';

    /**
     * Image storage paths.
     */
    private const PATH_ORIGINAL  = 'images/original';
    private const PATH_THUMBNAIL = 'images/thumbnails';

    public function __construct(
        private readonly ImageRepositoryInterface $imageRepository,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Read Operations
    |--------------------------------------------------------------------------
    */

    public function findByEntity(string $entityId): ?Image
    {
        return $this->imageRepository->findByEntity($entityId);
    }

    public function findByHash(string $hash): ?Image
    {
        return $this->imageRepository->findByHash($hash);
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Pipeline
    |--------------------------------------------------------------------------
    | 1. Store original
    | 2. Process thumbnail (crop → resize → WEBP)
    | 3. Generate SHA-256 hash
    | 4. Upsert database record
    | 5. Delete old files if replacing
    */

    public function upload(string $entityId, UploadedFile $file): Image
    {
        // Delete existing image if any
        $existing = $this->imageRepository->findByEntity($entityId);
        if ($existing) {
            $this->deleteFiles($existing);
        }

        // 1. Store original
        $originalPath = $this->storeOriginal($file);

        // 2. Process & store thumbnail
        $thumbnailPath = $this->processThumbnail($file);

        // 3. Get thumbnail metadata
        $thumbnailFullPath = Storage::disk(self::DISK)->path($thumbnailPath);
        $thumbnailSize     = Storage::disk(self::DISK)->size($thumbnailPath);

        // 4. Generate hash from thumbnail content
        $hash = $this->generateHash(
            Storage::disk(self::DISK)->get($thumbnailPath)
        );

        // 5. Upsert database record
        return $this->imageRepository->upsert($entityId, [
            'original_path'  => $originalPath,
            'thumbnail_path' => $thumbnailPath,
            'hash'           => $hash,
            'width'          => self::THUMBNAIL_SIZE,
            'height'         => self::THUMBNAIL_SIZE,
            'size'           => $thumbnailSize,
        ]);
    }

    public function delete(string $entityId): bool
    {
        $image = $this->imageRepository->findByEntity($entityId);

        if (! $image) {
            return false;
        }

        $this->deleteFiles($image);

        return $this->imageRepository->deleteByEntity($entityId);
    }

    /*
    |--------------------------------------------------------------------------
    | Hash Operations
    |--------------------------------------------------------------------------
    */

    public function verifyHash(string $entityId, string $clientHash): bool
    {
        $image = $this->imageRepository->findByEntity($entityId);

        if (! $image) {
            return false;
        }

        return $image->hash === $clientHash;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    private function storeOriginal(UploadedFile $file): string
    {
        return $file->store(self::PATH_ORIGINAL, self::DISK);
    }

    private function processThumbnail(UploadedFile $file): string
    {
        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        $path     = self::PATH_THUMBNAIL . '/' . $filename;
        $fullPath = Storage::disk(self::DISK)->path($path);

        // Ensure directory exists
        Storage::disk(self::DISK)->makeDirectory(self::PATH_THUMBNAIL);

        // Process image: crop to square → resize → encode as WEBP
        $image = ImageProcessor::read($file->getPathname())
            ->cover(self::THUMBNAIL_SIZE, self::THUMBNAIL_SIZE)
            ->toWebp(quality: 85);

        // Save to storage
        file_put_contents($fullPath, $image);

        return $path;
    }

    private function deleteFiles(Image $image): void
    {
        if ($image->original_path && Storage::disk(self::DISK)->exists($image->original_path)) {
            Storage::disk(self::DISK)->delete($image->original_path);
        }

        if ($image->thumbnail_path && Storage::disk(self::DISK)->exists($image->thumbnail_path)) {
            Storage::disk(self::DISK)->delete($image->thumbnail_path);
        }
    }
}