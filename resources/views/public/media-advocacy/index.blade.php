<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Media Advocacy']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Media Advocacy</h1>
                <p class="mt-1.5 text-navy-200">Request a media service and our team will follow up with a price.</p>
            </div>
            <x-button :href="route('media-advocacy.published')" variant="secondary" size="sm">See Published Media</x-button>
        </div>

        <form method="GET" class="mt-6 max-w-sm">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-navy-400" />
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search services" class="form-input pl-9">
            </div>
        </form>

        @if ($categories->isEmpty())
            <x-empty-state icon="megaphone" title="No services available" description="Media advocacy services will appear here once the admin team adds them." class="mt-8" />
        @else
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $cat)
                    <div class="card overflow-hidden">
                        <div class="flex h-36 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                            @if ($cat->image_url)
                                <img src="{{ $cat->image_url }}" class="h-full w-full object-cover">
                            @else
                                <x-icon :name="$cat->icon ?: 'megaphone'" class="h-10 w-10" />
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-navy-50 text-navy-600 dark:bg-navy-800 dark:text-navy-300">
                                    <x-icon :name="$cat->icon ?: 'megaphone'" class="h-4 w-4" />
                                </span>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $cat->name }}</p>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $cat->description }}</p>

                            @auth
                                <form method="POST" action="{{ route('media-advocacy.inquire', $cat) }}" class="mt-4">
                                    @csrf
                                    <x-button type="submit" class="w-full" size="sm">I'm Interested</x-button>
                                </form>
                            @else
                                <x-button :href="route('login')" class="mt-4 w-full" size="sm">I'm Interested</x-button>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
      </div>
    </div>
</x-layouts::app>
