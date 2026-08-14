<x-layouts::admin :title="'Verified Alumni'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Verified Alumni</h1>

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="form-input max-w-sm">
    </form>

    @include('admin.alumni._table')
</x-layouts::admin>
