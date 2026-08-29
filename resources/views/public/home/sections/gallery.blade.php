<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
    <div class="section-container py-5 sm:py-7">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white sm:text-3xl">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionName('gallery') }}</h2>
                <p class="mt-1.5 text-navy-200">{{ \App\Http\Controllers\Admin\SettingsController::resolveSectionDescription('gallery') }}</p>
            </div>
            <a href="{{ route('gallery.index') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
                View gallery <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        @if ($gallery->isEmpty())
            <x-empty-state icon="image" title="No gallery photos yet" class="mt-8" />
        @else
            <div class="mt-8">
                <x-gallery-grid :photos="$gallery" :compact="true" />
            </div>
        @endif
    </div>
</section>
