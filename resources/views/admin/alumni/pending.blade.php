<x-layouts::admin :title="'Pending Verification'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pending Verification</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review and approve new alumni registrations.</p>

    <form method="GET" class="mt-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email" class="form-input max-w-sm">
    </form>

    @include('admin.alumni._table')
</x-layouts::admin>
