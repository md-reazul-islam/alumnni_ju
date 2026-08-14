<x-layouts::admin :title="'Homepage Slider'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Homepage Slider</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage the interactive slides shown at the top of the public homepage.</p>
        </div>
        <x-button :href="route('admin.sliders.create')" size="sm">
            <x-icon name="plus" class="h-4 w-4" /> Add Slide
        </x-button>
    </div>

    @if ($sliders->isEmpty())
        <x-empty-state icon="image" title="No slides yet" description="Add your first slide to populate the homepage hero." class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Button</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sliders as $slider)
                    <tr>
                        <td><img src="{{ $slider->image_url }}" class="h-12 w-20 rounded object-cover"></td>
                        <td class="font-medium text-slate-900 dark:text-white">
                            {{ $slider->title }}
                            @if ($slider->subtitle)
                                <p class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ $slider->subtitle }}</p>
                            @endif
                        </td>
                        <td>{{ $slider->button_text ?: '—' }}</td>
                        <td>{{ $slider->position }}</td>
                        <td>
                            <x-badge :variant="$slider->is_active ? 'success' : 'neutral'">
                                {{ $slider->is_active ? 'Active' : 'Hidden' }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-slate-400 hover:text-navy-700" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" onsubmit="event.preventDefault(); confirmDelete(this, '{{ $slider->title }}');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600" title="Delete"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    @endif

    @push('scripts')
    <script>
        function confirmDelete(form, title) {
            Swal.fire({
                title: `Delete "${title}"?`,
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc2626',
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        }
    </script>
    @endpush
</x-layouts::admin>
