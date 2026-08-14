<x-layouts::admin :title="'All Alumni'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">All Alumni</h1>
        <x-button :href="route('admin.alumni.export')" variant="secondary" size="sm"><x-icon name="download" class="h-4 w-4" /> Export CSV</x-button>
    </div>

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="form-input max-w-sm">
    </form>

    @include('admin.alumni._table')
</x-layouts::admin>
