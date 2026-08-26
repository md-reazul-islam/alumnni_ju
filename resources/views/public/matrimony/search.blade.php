<x-layouts::app>
    <div>
      <div class="section-container py-8">
        <x-breadcrumb :items="[['label' => 'Matrimony']]" onDark class="mb-4" />

        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Find a Match</h1>
                <p class="mt-1.5 text-navy-200">Browse profiles reviewed and approved by our team. Request an introduction to see full details.</p>
            </div>
            @auth
                <x-button :href="route('matrimony.profiles.mine')" size="sm">My Profiles</x-button>
            @endauth
        </div>

        <form method="GET" action="{{ route('matrimony.search') }}" class="card card-body mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <x-select label="Gender" name="gender" :options="['male' => 'Male', 'female' => 'Female']" :selected="$filters['gender'] ?? null" placeholder="Any" />
            <x-input label="Min Age" name="age_min" type="number" min="18" max="90" :value="$filters['age_min'] ?? null" />
            <x-input label="Max Age" name="age_max" type="number" min="18" max="90" :value="$filters['age_max'] ?? null" />
            <x-input label="Country" name="country" :value="$filters['country'] ?? null" placeholder="e.g. USA" />
            <x-input label="Nationality" name="nationality" :value="$filters['nationality'] ?? null" placeholder="e.g. Bangladeshi" />
            <x-input label="Religion" name="religion" :value="$filters['religion'] ?? null" />
            <x-select label="Marital Status" name="marital_status" :options="['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated']" :selected="$filters['marital_status'] ?? null" placeholder="Any" />
            <x-input label="Keyword" name="keyword" :value="$filters['keyword'] ?? null" placeholder="Occupation, education..." />
            <div class="flex items-end sm:col-span-3 lg:col-span-4">
                <x-button type="submit">Search</x-button>
            </div>
        </form>

        @if ($profiles->isEmpty())
            <x-empty-state icon="heart" title="No matching profiles" description="Try broadening your filters, or check back soon — approved profiles appear here as they're reviewed." class="mt-8" />
        @else
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($profiles as $profile)
                    <a href="{{ route('matrimony.show', $profile) }}" class="card overflow-hidden transition hover:shadow-popover">
                        <div class="flex aspect-square items-center justify-center bg-slate-100 dark:bg-navy-800">
                            @if ($profile->photoVisibleTo(auth()->user()) && $profile->primary_photo)
                                <img src="{{ asset('storage/' . $profile->primary_photo->path) }}" class="h-full w-full object-cover">
                            @else
                                <x-icon name="user" class="h-10 w-10 text-slate-300 dark:text-navy-600" />
                            @endif
                        </div>
                        <div class="p-2.5">
                            <div class="flex items-center gap-1">
                                <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $profile->display_name }}, {{ $profile->age }}</p>
                                @if ($profile->is_verified)
                                    <x-icon name="badge-check" class="h-3.5 w-3.5 flex-shrink-0 text-navy-600 dark:text-navy-300" />
                                @endif
                            </div>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $profile->city ? $profile->city . ', ' : '' }}{{ $profile->country }}</p>
                            <p class="mt-0.5 truncate text-[11px] text-slate-400">{{ $profile->occupation }} &middot; {{ $profile->religion }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">{{ $profiles->links() }}</div>
        @endif
      </div>
    </div>
</x-layouts::app>
