<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-3xl py-12">
        <x-breadcrumb :items="[['label' => 'Donate', 'url' => route('donations.index')], ['label' => $campaign->title]]" class="mb-6" />

        <div class="card overflow-hidden">
            @if ($campaign->image_url)
                <img src="{{ $campaign->image_url }}" class="h-56 w-full object-cover" alt="{{ $campaign->title }}">
            @endif

            <div class="card-body">
                <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $campaign->category)) }}</x-badge>
                <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">{{ $campaign->title }}</h1>

                @if ($campaign->goal_amount)
                    <div class="mt-4 h-2.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-navy-800">
                        <div class="h-full rounded-full bg-gold-500" style="width: {{ $campaign->progressPercentage() }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-semibold text-slate-900 dark:text-white">${{ number_format((float) $campaign->raised_amount) }}</span>
                        raised of ${{ number_format((float) $campaign->goal_amount) }} goal
                    </p>
                @endif

                <div class="prose prose-slate mt-6 max-w-none dark:prose-invert">
                    <p class="whitespace-pre-line">{{ $campaign->description }}</p>
                </div>

                @if (Route::has('donations.checkout'))
                    <x-button :href="route('donations.checkout', $campaign)" variant="gold" class="mt-6">Donate to This Campaign</x-button>
                @endif
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
