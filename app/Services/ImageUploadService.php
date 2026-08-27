<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageUploadService
{
    /**
     * Long-edge cap (px) for photo-like content displayed at meaningful size:
     * listing/gallery/profile photos, covers, hero images.
     */
    public const MAX_LARGE = 1600;

    /**
     * Long-edge cap (px) for content always displayed small: avatars, logos.
     */
    public const MAX_SMALL = 800;

    protected const RASTER_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    protected const RASTER_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process and store an uploaded image: auto-orients from EXIF, downscales to fit
     * within $maxDimension on the long edge (never upscales, never crops), strips
     * metadata, and re-encodes at a consistent quality. Non-raster files (svg, ico,
     * anything not in RASTER_MIMES) are stored unprocessed since they aren't safe or
     * meaningful to raster-resize.
     */
    public function store(UploadedFile $file, string $directory, int $maxDimension = self::MAX_LARGE, string $disk = 'public'): string
    {
        if (! in_array($file->getMimeType(), self::RASTER_MIMES, true)) {
            return $file->store($directory, $disk);
        }

        $image = $this->manager->read($file->getRealPath());
        $image->scaleDown($maxDimension, $maxDimension);

        $extension = strtolower($file->getClientOriginalExtension());
        $extension = in_array($extension, self::RASTER_EXTENSIONS, true) ? $extension : 'jpg';

        $encoded = match ($extension) {
            'png' => $image->toPng(),
            'webp' => $image->toWebp(quality: 82),
            'gif' => $image->toGif(),
            default => $image->toJpeg(quality: 82),
        };

        $path = trim($directory, '/') . '/' . Str::random(40) . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
        Storage::disk($disk)->put($path, (string) $encoded);

        return $path;
    }

    public function delete(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Reprocess an already-stored image IN PLACE (same path, same filename) — for
     * backfilling images that were uploaded before this service existed. Only touches
     * the file if it actually needs help (oversized, or a non-normal EXIF rotation
     * tag); otherwise leaves it untouched. This makes repeat runs safe: a file this
     * has already fixed (or a freshly-uploaded one that was already processed by
     * store()) won't get a second lossy re-encode pass.
     *
     * Returns a status string: 'missing', 'skipped-non-raster', 'already-ok', or 'processed'.
     * Pass $dryRun = true to compute the status without writing anything.
     */
    public function reprocessInPlace(string $path, int $maxDimension = self::MAX_LARGE, string $disk = 'public', bool $dryRun = false): string
    {
        if (! Storage::disk($disk)->exists($path)) {
            return 'missing';
        }

        $mimeType = Storage::disk($disk)->mimeType($path);
        if (! in_array($mimeType, self::RASTER_MIMES, true)) {
            return 'skipped-non-raster';
        }

        $fullPath = Storage::disk($disk)->path($path);

        [$width, $height] = @getimagesize($fullPath) ?: [0, 0];

        $orientation = 1;
        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($fullPath);
            $orientation = $exif['Orientation'] ?? 1;
        }

        $needsResize = $width > $maxDimension || $height > $maxDimension;
        $needsRotation = $orientation !== 1;

        if (! $needsResize && ! $needsRotation) {
            return 'already-ok';
        }

        if ($dryRun) {
            return 'processed';
        }

        $image = $this->manager->read($fullPath);
        $image->scaleDown($maxDimension, $maxDimension);

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $extension = in_array($extension, self::RASTER_EXTENSIONS, true) ? $extension : 'jpg';

        $encoded = match ($extension) {
            'png' => $image->toPng(),
            'webp' => $image->toWebp(quality: 82),
            'gif' => $image->toGif(),
            default => $image->toJpeg(quality: 82),
        };

        Storage::disk($disk)->put($path, (string) $encoded);

        return 'processed';
    }
}
