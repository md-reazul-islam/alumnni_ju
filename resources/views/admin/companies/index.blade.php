<x-layouts::admin :title="'Companies'">
    <div x-data="{ adding: false }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Companies</h1>
            <x-button size="sm" @click="adding = !adding"><x-icon name="plus" class="h-4 w-4" /> Add Company</x-button>
        </div>

        <form method="POST" action="{{ route('admin.companies.store') }}" enctype="multipart/form-data" x-show="adding" x-cloak class="card card-body mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Company Name" name="name" required />
                <x-input label="Website" name="website" type="url" />
                <x-input label="Industry" name="industry" />
                <div>
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="form-input">
                </div>
            </div>
            <x-textarea label="Description" name="description" rows="3" />
            <div class="flex justify-end"><x-button type="submit" size="sm">Save</x-button></div>
        </form>

        <form method="GET" class="mt-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies" class="form-input max-w-xs">
        </form>

        @if ($companies->isEmpty())
            <x-empty-state icon="building-2" title="No companies yet" class="mt-8" />
        @else
            <x-table class="mt-6">
                <thead><tr><th>Name</th><th>Industry</th><th>Job Postings</th><th></th></tr></thead>
                <tbody>
                    @foreach ($companies as $company)
                        <tr>
                            <td class="flex items-center gap-2 font-medium text-slate-900 dark:text-white">
                                @if ($company->logo_url)<img src="{{ $company->logo_url }}" class="h-6 w-6 rounded object-cover">@endif
                                {{ $company->name }}
                            </td>
                            <td>{{ $company->industry }}</td>
                            <td>{{ $company->job_postings_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this company?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-6">{{ $companies->links() }}</div>
        @endif
    </div>
</x-layouts::admin>
