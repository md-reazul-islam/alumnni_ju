<?php

namespace App\Console\Commands;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Book;
use App\Models\CarpoolCar;
use App\Models\CateringFoodItem;
use App\Models\CateringHomemadeListingImage;
use App\Models\CommunityPost;
use App\Models\Company;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\MarketplaceListingImage;
use App\Models\MatrimonyProfilePhoto;
use App\Models\News;
use App\Models\Slider;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Console\Command;

class ReprocessUploadedImages extends Command
{
    protected $signature = 'images:reprocess {--dry-run : Report what would change without touching any files}';

    protected $description = 'Backfill: reprocess every already-uploaded image in place (auto-orient, downscale to the standard cap, re-encode) so images uploaded before ImageUploadService existed match the same consistent sizing as new uploads.';

    /**
     * [Model class, image column, max-dimension preset]. Each target is reprocessed
     * independently; the column's value is used as the storage path, unchanged.
     */
    protected const TARGETS = [
        [User::class, 'avatar', ImageUploadService::MAX_SMALL],
        [AlumniProfile::class, 'cover_image', ImageUploadService::MAX_LARGE],
        [CateringFoodItem::class, 'image', ImageUploadService::MAX_LARGE],
        [GalleryPhoto::class, 'image', ImageUploadService::MAX_LARGE],
        [Book::class, 'cover', ImageUploadService::MAX_LARGE],
        [AlumniStory::class, 'cover_image', ImageUploadService::MAX_LARGE],
        [CarpoolCar::class, 'photo', ImageUploadService::MAX_LARGE],
        [CommunityPost::class, 'image', ImageUploadService::MAX_LARGE],
        [Company::class, 'logo', ImageUploadService::MAX_SMALL],
        [DonationCampaign::class, 'image', ImageUploadService::MAX_LARGE],
        [Event::class, 'image', ImageUploadService::MAX_LARGE],
        [News::class, 'featured_image', ImageUploadService::MAX_LARGE],
        [Slider::class, 'image', ImageUploadService::MAX_LARGE],
        [MarketplaceListingImage::class, 'path', ImageUploadService::MAX_LARGE],
        [CateringHomemadeListingImage::class, 'path', ImageUploadService::MAX_LARGE],
        [MatrimonyProfilePhoto::class, 'path', ImageUploadService::MAX_LARGE],
    ];

    public function handle(ImageUploadService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Dry run — no files will be modified.');
        }

        $totals = ['processed' => 0, 'already-ok' => 0, 'skipped-non-raster' => 0, 'missing' => 0];

        foreach (self::TARGETS as [$modelClass, $column, $maxDimension]) {
            $label = class_basename($modelClass) . "::{$column}";
            $query = $modelClass::query()->whereNotNull($column)->where($column, '!=', '');
            $count = $query->count();

            if ($count === 0) {
                $this->line("{$label}: no images");
                continue;
            }

            $this->line("{$label}: {$count} image(s)");
            $counts = ['processed' => 0, 'already-ok' => 0, 'skipped-non-raster' => 0, 'missing' => 0];

            $query->orderBy('id')->chunkById(50, function ($records) use ($column, $maxDimension, $dryRun, $service, &$counts) {
                foreach ($records as $record) {
                    $path = $record->{$column};
                    $status = $service->reprocessInPlace($path, $maxDimension, dryRun: $dryRun);
                    $counts[$status] = ($counts[$status] ?? 0) + 1;
                }
            });

            foreach ($counts as $status => $n) {
                $totals[$status] += $n;
            }

            $this->line("  processed={$counts['processed']} already-ok={$counts['already-ok']} skipped-non-raster={$counts['skipped-non-raster']} missing={$counts['missing']}");
        }

        $this->newLine();
        $this->info(($dryRun ? 'Would process' : 'Processed') . " {$totals['processed']} image(s) total. Already fine: {$totals['already-ok']}. Skipped (non-raster): {$totals['skipped-non-raster']}. Missing files: {$totals['missing']}.");

        return self::SUCCESS;
    }
}
