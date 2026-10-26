<?php

namespace App\Services\Media;

use App\Enums\MediaType;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MediaService
{
    private const DISK = 'public';
    private const MAX_IMAGES = 10;
    private const MAX_VIDEOS = 2;
    private const MAX_IMAGE_KB = 5120;

    /** @param UploadedFile[] $files */
    public function attach(Model $model, array $files, User $uploader, string $collection = 'default'): array
    {
        $this->ensureLimits($model, $files, $collection);

        $storedPaths = [];

        try {
            return DB::transaction(function () use ($model, $files, $uploader, $collection, &$storedPaths) {
                $created = [];

                foreach ($files as $file) {
                    $path = $file->store("media/{$model->getTable()}/{$model->getKey()}", self::DISK);
                    $storedPaths[] = $path;

                    $created[] = $model->media()->create([
                        'collection'    => $collection,
                        'type'          => $this->detectType($file),
                        'disk'          => self::DISK,
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getMimeType(),
                        'size'          => $file->getSize(),
                        'uploaded_by'   => $uploader->id,
                    ]);
                }

                return $created;
            });
        } catch (\Throwable $e) {
            Storage::disk(self::DISK)->delete($storedPaths);
            throw $e;
        }
    }

    public function replace(Media $media, UploadedFile $file): Media
    {
        $type = $this->detectType($file);

          if ($type !== $media->type) {
            throw ValidationException::withMessages([
                'file' => "The replacement must be of the same type ({$media->type->value}).",
            ]);
        }

        $this->ensureImageSize($file, 'file');

        $oldPath = $media->path;
        $newPath = $file->store(dirname($oldPath), self::DISK);

        $media->update([
            'path'          => $newPath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);

        Storage::disk($media->disk)->delete($oldPath);

        return $media->refresh();
    }

    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();
    }

    private function detectType(UploadedFile $file): MediaType
    {
        return str_starts_with((string) $file->getMimeType(), 'video/')
            ? MediaType::Video
            : MediaType::Image;
    }

    private function ensureImageSize(UploadedFile $file, string $key): void
    {
        if ($this->detectType($file) === MediaType::Image && $file->getSize() > self::MAX_IMAGE_KB * 1024) {
            throw ValidationException::withMessages([
                $key => 'Each image must not exceed 5MB.',
            ]);
        }
    }

    private function ensureLimits(Model $model, array $files, string $collection): void
    {
        $newImages = $newVideos = 0;

        foreach ($files as $index => $file) {
            $this->ensureImageSize($file, "files.{$index}");
            $this->detectType($file) === MediaType::Video ? $newVideos++ : $newImages++;
        }

        $existing = $model->media()->where('collection', $collection);

        $images = (clone $existing)->where('type', MediaType::Image->value)->count();
        $videos = (clone $existing)->where('type', MediaType::Video->value)->count();

        if ($images + $newImages > self::MAX_IMAGES) {
            throw ValidationException::withMessages([
                'files' => 'Maximum ' . self::MAX_IMAGES . ' images allowed.',
            ]);
        }

        if ($videos + $newVideos > self::MAX_VIDEOS) {
            throw ValidationException::withMessages([
                'files' => 'Maximum ' . self::MAX_VIDEOS . ' videos allowed.',
            ]);
        }
    }
}
