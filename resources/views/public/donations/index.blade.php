<x-layouts::app>
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
      <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'Donate']]" class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Give Back</h1>
                <p class="mt-1.5 text-slate-500 dark:text-slate-400">Support scholarships, research, and student programs through active campaigns.</p>
            </div>
            <x-button :href="route('donations.checkout')" variant="gold" size="sm">Donate Now</x-button>
        </div>

        @if ($campaigns->isEmpty())
            <x-empty-state icon="heart" title="No active campaigns" description="New giving campaigns will be announced here." class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($campaigns as $campaign)
                    <a href="{{ route('donations.show', $campaign) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex h-32 items-center justify-center bg-gold-50 text-gold-500 dark:bg-navy-800">
                            @if ($campaign->image_url)
                                <img src="{{ $campaign->image_url }}" class="h-full w-full object-cover" alt="{{ $campaign->title }}">
                            @else
                                <x-icon name="heart" class="h-9 w-9" />
                            @endif
                        </div>
                        <div class="card-body">
                            <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $campaign->category)) }}</x-badge>
                            <p class="mt-2 font-semibold text-slate-900 dark:text-white">{{ $campaign->title }}</p>

                            @if ($campaign->goal_amount)
                                <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-navy-800">
                                    <div class="h-full rounded-full bg-gold-500" style="width: {{ $campaign->progressPercentage() }}%"></div>
                                </div>
                                <p class="mt-1.5 text-xs text-slate-400">
                                    ${{ number_format((float) $campaign->raised_amount) }} raised of ${{ number_format((float) $campaign->goal_amount) }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $campaigns->links() }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
