<x-layouts::alumni :title="'Who Viewed My Profile'">
    <x-breadcrumb :items="[['label' => 'My Matrimony Profiles', 'url' => route('matrimony.profiles.mine')], ['label' => 'Edit Profile', 'url' => route('matrimony.profiles.edit', $profile)], ['label' => 'Viewers']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Who Viewed "{{ $profile->display_name }}"</h1>

    @if ($viewers->isEmpty())
        <x-empty-state icon="eye" title="No views yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-3">
            @foreach ($viewers as $view)
                <div class="card card-body flex items-center justify-between">
                    <p class="font-medium text-slate-900 dark:text-white">{{ $view->viewer?->full_name ?? 'Anonymous visitor' }}</p>
                    <p class="text-sm text-slate-400">{{ $view->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $viewers->links() }}</div>
    @endif
</x-layouts::alumni>
