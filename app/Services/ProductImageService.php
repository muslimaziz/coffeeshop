<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\ImageManagerInterface;

class ProductImageService
{
    private const MAX_DIMENSION = 1200;

    private const QUALITY = 80;

    public function __construct(private readonly ImageManagerInterface $manager) {}

    /**
     * Store an uploaded image, resized and compressed for the web.
     */
    public function store(UploadedFile $file, string $folder = 'products', ?int $maxWidth = null, ?int $maxHeight = null): string
    {
        $path = $file->store($folder, 'public');

        try {
            $fullPath = Storage::disk('public')->path($path);

            $image = $this->manager->decodePath($fullPath);
            $image->orient();
            $image->scaleDown($maxWidth ?? self::MAX_DIMENSION, $maxHeight ?? self::MAX_DIMENSION);
            $image->save($fullPath, quality: self::QUALITY);
        } catch (\Throwable $e) {
            Log::warning('Gagal memproses gambar: '.$e->getMessage());
        }

        return $path;
    }

    /**
     * Store an uploaded image, cropped to an exact aspect ratio.
     */
    public function storeCropped(UploadedFile $file, string $folder, int $width, int $height): string
    {
        $path = $file->store($folder, 'public');

        try {
            $fullPath = Storage::disk('public')->path($path);

            $image = $this->manager->decodePath($fullPath);
            $image->orient();
            $image->cover($width, $height);
            $image->save($fullPath, quality: self::QUALITY);
        } catch (\Throwable $e) {
            Log::warning('Gagal memproses gambar: '.$e->getMessage());
        }

        return $path;
    }
}
