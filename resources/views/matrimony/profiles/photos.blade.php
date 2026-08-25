<x-layouts::alumni :title="'Manage Photos'">
    <x-breadcrumb :items="[['label' => 'My Matrimony Profiles', 'url' => route('matrimony.profiles.mine')], ['label' => 'Edit Profile', 'url' => route('matrimony.profiles.edit', $profile)], ['label' => 'Photos']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manage Photos</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
        Photo visibility is currently set to <strong>{{ ucfirst($profile->photo_visibility) }}</strong> — change it from the profile edit form.
    </p>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @error('photos')
        <x-alert variant="danger" class="mt-4">{{ $message }}</x-alert>
    @enderror
    @error('photos.0')
        <x-alert variant="danger" class="mt-4">{{ $message }}</x-alert>
    @enderror

    <div class="card card-body mt-6">
        @if ($profile->photos->isEmpty())
            <x-empty-state icon="camera" title="No photos yet" class="py-6" />
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($profile->photos as $photo)
                    <div class="relative overflow-hidden rounded-lg border border-slate-100 dark:border-navy-800">
                        <img src="{{ asset('storage/' . $photo->path) }}" class="aspect-square w-full object-cover">
                        @if ($photo->is_primary)
                            <span class="absolute left-2 top-2 rounded-full bg-navy-700 px-2 py-0.5 text-[10px] font-semibold text-white">Primary</span>
                        @endif
                        <div class="flex items-center justify-between gap-1 bg-white p-2 dark:bg-navy-900">
                            @unless ($photo->is_primary)
                                <form method="POST" action="{{ route('matrimony.photos.set-primary', $photo) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-navy-700 hover:underline dark:text-navy-300">Set Primary</button>
                                </form>
                            @else
                                <span></span>
                            @endunless
                            <form method="POST" action="{{ route('matrimony.photos.destroy', $photo) }}" onsubmit="return confirm('Remove this photo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($profile->photos->count() < 6)
            <form method="POST" action="{{ route('matrimony.photos.store', $profile) }}" enctype="multipart/form-data" class="mt-6 space-y-3">
                @csrf
                <label class="form-label">Add Photos</label>
                <input type="file" name="photos[]" accept="image/*" multiple class="form-input">
                <p class="form-hint">Up to {{ 6 - $profile->photos->count() }} more photo(s), JPG/PNG/WEBP, 4MB max each.</p>
                <x-button type="submit" size="sm">Upload</x-button>
            </form>
        @endif
    </div>
</x-layouts::alumni>
