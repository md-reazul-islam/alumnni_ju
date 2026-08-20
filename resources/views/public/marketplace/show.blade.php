<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-4xl py-8">
        <x-breadcrumb :items="[['label' => 'Marketplace', 'url' => route('marketplace.index')], ['label' => $listing->title]]" class="mb-6" />

        <div class="card card-body">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $listing->title }}</h1>
                    <p class="mt-1 flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <x-icon name="map-pin" class="h-4 w-4" /> {{ $listing->address }}{{ $listing->city ? ', ' . $listing->city : '' }}
                    </p>
                </div>
                <p class="text-2xl font-bold text-navy-700 dark:text-gold-400">
                    ${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}
                </p>
            </div>

            @if ($listing->images->isNotEmpty())
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($listing->images as $image)
                        <img src="{{ asset('storage/' . $image->path) }}" class="h-28 w-full rounded-lg object-cover">
                    @endforeach
                </div>
            @endif

            <div class="prose prose-slate mt-8 max-w-none dark:prose-invert">
                <h2>Description</h2>
                <p class="whitespace-pre-line">{{ $listing->description }}</p>
            </div>

            @if (!empty($listing->details))
                <div class="mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Details</h2>
                    <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        @foreach ($listing->details as $detail)
                            <div>
                                <dt class="text-slate-400">{{ $detail['label'] }}</dt>
                                <dd class="font-medium text-slate-700 dark:text-slate-200">{{ $detail['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if ($listing->video_embed_url)
                <div class="mt-6">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Video</h2>
                    <div class="mt-3 aspect-video overflow-hidden rounded-lg">
                        <iframe src="{{ $listing->video_embed_url }}" class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <h2 class="font-semibold text-slate-900 dark:text-white">Location</h2>
                <div class="mt-3 overflow-hidden rounded-lg">
                    <iframe src="{{ $listing->map_embed_url }}" class="h-64 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6 dark:border-navy-800">
                @auth
                    @if ($hasActiveInquiry)
                        <x-badge variant="success" class="text-sm">You've already contacted our team about this listing — check your messages.</x-badge>
                    @else
                        <form method="POST" action="{{ route('marketplace.inquire', $listing) }}">
                            @csrf
                            <x-button type="submit">I'm Interested</x-button>
                        </form>
                        <p class="mt-2 text-xs text-slate-400">Interested buyers are connected through our office — we'll pass your inquiry along and follow up with you directly.</p>
                    @endif
                @else
                    <x-button :href="route('login')">Log In to Contact Us About This Listing</x-button>
                @endauth
            </div>
        </div>
    </div>
  </div>
</x-layouts::app>
