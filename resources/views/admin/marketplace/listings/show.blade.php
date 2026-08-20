<x-layouts::admin :title="$listing->title">
    <x-breadcrumb :items="[['label' => 'Marketplace'], ['label' => 'Pending Listings', 'url' => route('admin.marketplace.listings.pending')], ['label' => $listing->title]]" class="mb-4" />

    <div x-data="{ rejecting: false }">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <x-badge variant="info">{{ $listing->category->name }}</x-badge>
                    <x-badge :variant="match($listing->status) { 'approved' => 'success', 'rejected' => 'danger', 'expired' => 'neutral', default => 'warning' }">
                        {{ ucfirst($listing->status) }}
                    </x-badge>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $listing->title }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Posted by {{ $listing->seller->full_name }} ({{ $listing->seller->email }}) &middot; {{ $listing->created_at->format('M j, Y') }}
                </p>
            </div>

            @if ($listing->status === 'pending')
                <div class="flex flex-shrink-0 gap-2">
                    <form method="POST" action="{{ route('admin.marketplace.listings.approve', $listing) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Approve</button>
                    </form>
                    <button type="button" @click="rejecting = !rejecting" class="btn-secondary btn-sm">Reject</button>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.marketplace.listings.reject', $listing) }}" x-show="rejecting" x-cloak class="card card-body mt-4 space-y-3">
            @csrf
            <x-textarea label="Reason for rejection" name="rejection_reason" rows="3" required placeholder="Explain what needs to change so the seller knows why." />
            <div class="flex justify-end"><x-button type="submit" variant="danger" size="sm">Confirm Rejection</x-button></div>
        </form>

        @if ($listing->status === 'rejected' && $listing->rejection_reason)
            <x-alert variant="danger" class="mt-4">{{ $listing->rejection_reason }}</x-alert>
        @endif

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @if ($listing->images->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($listing->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" class="h-28 w-full rounded-lg object-cover">
                        @endforeach
                    </div>
                @endif

                <div class="card card-body">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Description</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $listing->description }}</p>
                </div>

                @if (!empty($listing->details))
                    <div class="card card-body">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Additional Details</h2>
                        <dl class="mt-2 grid grid-cols-2 gap-3 text-sm">
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
                    <div class="card card-body">
                        <h2 class="font-semibold text-slate-900 dark:text-white">Video</h2>
                        <div class="mt-2 aspect-video overflow-hidden rounded-lg">
                            <iframe src="{{ $listing->video_embed_url }}" class="h-full w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="card card-body">
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">
                        ${{ number_format($listing->price, 2) }}{{ $listing->price_unit !== 'total' ? ' / ' . str_replace('per_', '', $listing->price_unit) : '' }}
                    </p>
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                        <x-icon name="map-pin" class="h-4 w-4" /> {{ $listing->address }}{{ $listing->city ? ', ' . $listing->city : '' }}
                    </p>
                </div>

                <div class="card overflow-hidden">
                    <iframe src="{{ $listing->map_embed_url }}" class="h-56 w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</x-layouts::admin>
