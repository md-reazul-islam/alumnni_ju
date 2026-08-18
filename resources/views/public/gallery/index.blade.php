<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Gallery']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Gallery</h1>
                <p class="mt-1.5 text-navy-200">Moments from reunions, events, and everyday life shared by our alumni community.</p>
            </div>
            @auth
                @if (Route::has('gallery.create'))
                    <x-button :href="route('gallery.create')" size="sm">
                        <x-icon name="plus" class="h-4 w-4" /> Add a Photo
                    </x-button>
                @endif
            @endauth
        </div>

        @if ($photos->isEmpty())
            <x-empty-state icon="image" title="No gallery photos yet" description="Approved photos shared by alumni and admins will appear here." class="mt-8" />
        @else
            <div class="mt-8">
                <x-gallery-grid :photos="$photos" />
            </div>

            <div class="mt-8">{{ $photos->links('vendor.pagination.tailwind-dark') }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
