@props(['type', 'title', 'items', 'fields', 'primaryField', 'secondaryField' => null])

<div class="card card-body" x-data="{ adding: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
        <button type="button" @click="adding = !adding" class="flex items-center gap-1.5 text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300">
            <x-icon name="plus" class="h-4 w-4" /> <span x-text="adding ? 'Cancel' : 'Add'"></span>
        </button>
    </div>

    @if ($items->isEmpty())
        <p class="mt-3 text-sm text-slate-400">No entries yet.</p>
    @else
        <ul class="mt-3 divide-y divide-slate-100 dark:divide-navy-800">
            @foreach ($items as $item)
                <li class="flex items-center justify-between gap-3 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $item->{$primaryField} }}</p>
                        @if ($secondaryField && $item->{$secondaryField})
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($item->{$secondaryField}, 80) }}</p>
                        @endif
                    </div>
                    <form
                        method="POST"
                        action="{{ route('alumni.profile.items.destroy', [$type, $item->id]) }}"
                        x-data
                        @submit.prevent="
                            Swal.fire({
                                title: 'Remove this entry?',
                                text: 'This action cannot be undone.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Remove',
                                confirmButtonColor: '#dc2626',
                            }).then((result) => { if (result.isConfirmed) $el.submit(); })
                        "
                    >
                        @csrf @method('DELETE')
                        <button type="submit" class="flex-shrink-0 rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20">
                            <x-icon name="trash-2" class="h-4 w-4" />
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('alumni.profile.items.store', $type) }}" x-show="adding" x-cloak class="mt-4 space-y-4 border-t border-slate-100 pt-4 dark:border-navy-800">
        @csrf

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($fields as $field)
                <div class="{{ ($field['type'] ?? 'text') === 'textarea' ? 'sm:col-span-2' : '' }}">
                    @if (($field['type'] ?? 'text') === 'select')
                        <x-select :label="$field['label']" :name="$field['name']" :options="$field['options']" :required="$field['required'] ?? false" placeholder="Select" />
                    @elseif (($field['type'] ?? 'text') === 'textarea')
                        <x-textarea :label="$field['label']" :name="$field['name']" :required="$field['required'] ?? false" />
                    @else
                        <x-input :label="$field['label']" :name="$field['name']" :type="$field['type'] ?? 'text'" :required="$field['required'] ?? false" />
                    @endif
                </div>
            @endforeach
        </div>

        <div class="flex justify-end">
            <x-button type="submit" size="sm">Save Entry</x-button>
        </div>
    </form>
</div>
