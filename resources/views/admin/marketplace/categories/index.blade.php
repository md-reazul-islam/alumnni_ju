<x-layouts::admin :title="'Marketplace Categories'">
    <div x-data="{ adding: false, editing: null }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Marketplace Categories</h1>
            <x-button size="sm" @click="adding = !adding"><x-icon name="plus" class="h-4 w-4" /> Add Category</x-button>
        </div>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Commission percentage is applied to completed orders in this category — change it anytime.</p>

        @if (session('status'))
            <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.marketplace.categories.store') }}" x-show="adding" x-cloak class="card card-body mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <x-input label="Category Name" name="name" required />
                <x-input label="Icon (optional)" name="icon" hint="e.g. home, sofa, car" />
                <x-input label="Commission %" name="commission_percentage" type="number" step="0.01" min="0" max="100" value="0" required />
            </div>
            <div class="flex justify-end"><x-button type="submit" size="sm">Save</x-button></div>
        </form>

        <form method="GET" class="mt-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories" class="form-input max-w-xs">
        </form>

        @if ($categories->isEmpty())
            <x-empty-state icon="tags" title="No categories yet" class="mt-8" />
        @else
            <x-table class="mt-6">
                <thead><tr><th>Name</th><th>Commission %</th><th>Listings</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                            <td>{{ rtrim(rtrim(number_format($category->commission_percentage, 2), '0'), '.') }}%</td>
                            <td>{{ $category->listings_count }}</td>
                            <td>
                                <x-badge :variant="$category->is_active ? 'success' : 'neutral'">{{ $category->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="flex items-center gap-3">
                                <button type="button" @click="editing = editing === {{ $category->id }} ? null : {{ $category->id }}" class="text-slate-400 hover:text-navy-700">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>
                                <form method="POST" action="{{ route('admin.marketplace.categories.destroy', $category) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this category?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </td>
                        </tr>
                        <tr x-show="editing === {{ $category->id }}" x-cloak>
                            <td colspan="5">
                                <form method="POST" action="{{ route('admin.marketplace.categories.update', $category) }}" class="flex flex-wrap items-end gap-4 rounded-lg bg-slate-50 p-4 dark:bg-navy-900">
                                    @csrf @method('PUT')
                                    <x-input label="Category Name" name="name" :value="$category->name" required />
                                    <x-input label="Icon" name="icon" :value="$category->icon" />
                                    <x-input label="Commission %" name="commission_percentage" type="number" step="0.01" min="0" max="100" :value="$category->commission_percentage" required />
                                    <label class="flex items-center gap-2 pb-2 text-sm text-slate-600 dark:text-slate-300">
                                        <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                                        Active
                                    </label>
                                    <x-button type="submit" size="sm">Save</x-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-6">{{ $categories->links() }}</div>
        @endif
    </div>
</x-layouts::admin>
