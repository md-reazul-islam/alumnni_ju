<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Media Advocacy', 'url' => route('media-advocacy.index')], ['label' => $category->name]]" onDark class="mb-4" />

        <div class="mx-auto max-w-xl" x-data="{ showDetails: false }">
            <div class="card overflow-hidden">
                <div class="flex h-48 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                    @if ($category->image_url)
                        <img src="{{ $category->image_url }}" class="h-full w-full object-cover">
                    @else
                        <x-icon :name="$category->icon ?: 'megaphone'" class="h-16 w-16" />
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                            <x-icon :name="$category->icon ?: 'megaphone'" class="h-4 w-4" />
                        </span>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">{{ $category->name }}</h1>
                    </div>

                    <button
                        type="button"
                        @click="showDetails = !showDetails"
                        @mouseenter="showDetails = true"
                        @mouseleave="showDetails = false"
                        class="mt-4 flex items-center gap-1 text-sm font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 dark:hover:text-white"
                    >
                        <span class="transition-transform duration-200" :class="{ 'rotate-180': showDetails }">
                            <x-icon name="chevron-down" class="h-4 w-4" />
                        </span>
                        View Details
                    </button>

                    <div x-show="showDetails" x-transition x-cloak class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        {{ $category->description }}
                    </div>

                    @auth
                        <form method="POST" action="{{ route('media-advocacy.inquire', $category) }}" class="mt-5">
                            @csrf
                            <x-button type="submit" class="w-full">I'm Interested</x-button>
                        </form>
                    @else
                        <x-button :href="route('login')" class="mt-5 w-full">I'm Interested</x-button>
                    @endauth
                </div>
            </div>
        </div>
      </div>
    </div>
</x-layouts::app>
