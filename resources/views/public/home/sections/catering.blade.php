<section class="!mt-1.5 sm:!mt-2 lg:!mt-2.5">
  <div class="section-container py-5 sm:py-7">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Catering</h2>
            <p class="mt-1.5 text-navy-200">Order catering for your next event, or browse home made foods from fellow alumni.</p>
        </div>
        <a href="{{ route('catering.search') }}" class="flex items-center gap-1 text-sm font-semibold text-gold-400 hover:text-gold-300">
            Browse catering <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>

    @if ($cateringCategories->isEmpty())
        <x-empty-state icon="utensils" title="No catering categories yet" description="Program categories will appear here once the admin team adds them." class="mt-8" />
    @else
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-navy-200">Event Catering &middot; by program category</h3>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($cateringCategories as $cat)
                    <a href="{{ route('catering.search') }}" class="card flex flex-col items-center gap-2 p-3 text-center transition hover:shadow-popover sm:p-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                            <x-icon name="utensils" class="h-5 w-5" />
                        </span>
                        <span class="w-full truncate text-xs font-semibold text-slate-900 dark:text-white sm:text-sm">{{ $cat->name }}</span>
                        <span class="text-[10px] text-slate-400">{{ $cat->food_items_count }} {{ \Illuminate\Support\Str::plural('item', $cat->food_items_count) }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
  </div>
</section>
