<x-layouts::alumni :title="'My Favorites'">
    <x-breadcrumb :items="[['label' => 'My Favorites']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Favorites</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($favorites->isEmpty())
        <x-empty-state icon="heart" title="No favorites yet" description="Save profiles you're interested in to find them again easily." class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($favorites as $favorite)
                @continue(! $favorite->profile)
                <div class="card overflow-hidden">
                    <a href="{{ route('matrimony.show', $favorite->profile) }}" class="block">
                        <div class="flex aspect-[4/3] items-center justify-center bg-slate-100 dark:bg-navy-800">
                            @if ($favorite->profile->photo_visibility === 'public' && $favorite->profile->primary_photo)
                                <img src="{{ asset('storage/' . $favorite->profile->primary_photo->path) }}" class="h-full w-full object-cover">
                            @else
                                <x-icon name="user" class="h-16 w-16 text-slate-300 dark:text-navy-600" />
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $favorite->profile->display_name }}, {{ $favorite->profile->age }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $favorite->profile->city ? $favorite->profile->city . ', ' : '' }}{{ $favorite->profile->country }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('matrimony.favorites.destroy', $favorite->profile) }}" class="border-t border-slate-100 p-2 dark:border-navy-800">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-sm font-medium text-red-600 hover:underline">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts::alumni>
