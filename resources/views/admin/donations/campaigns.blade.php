<x-layouts::admin :title="'Donation Campaigns'">
    <div x-data="{ adding: false }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Donation Campaigns</h1>
            <x-button size="sm" @click="adding = !adding"><x-icon name="plus" class="h-4 w-4" /> New Campaign</x-button>
        </div>

        <form method="POST" action="{{ route('admin.donations.campaigns.store') }}" enctype="multipart/form-data" x-show="adding" x-cloak class="card card-body mt-4 space-y-4">
            @csrf
            <x-input label="Title" name="title" required />
            <x-textarea label="Description" name="description" rows="3" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-select label="Category" name="category" required :options="['scholarship' => 'Scholarship', 'research' => 'Research', 'student_support' => 'Student Support', 'infrastructure' => 'Infrastructure', 'emergency_fund' => 'Emergency Fund', 'alumni_association' => 'Alumni Association', 'general_fund' => 'General Fund']" />
                <x-input label="Goal Amount" name="goal_amount" type="number" />
            </div>
            <div>
                <label class="form-label">Campaign Image</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>
            <div class="flex justify-end"><x-button type="submit" size="sm">Create Campaign</x-button></div>
        </form>

        @if ($campaigns->isEmpty())
            <x-empty-state icon="heart" title="No campaigns yet" class="mt-8" />
        @else
            <x-table class="mt-6">
                <thead><tr><th>Title</th><th>Category</th><th>Raised / Goal</th><th>Donations</th><th></th></tr></thead>
                <tbody>
                    @foreach ($campaigns as $campaign)
                        <tr>
                            <td class="font-medium text-slate-900 dark:text-white">{{ $campaign->title }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $campaign->category)) }}</td>
                            <td>${{ number_format((float) $campaign->raised_amount) }} / ${{ number_format((float) $campaign->goal_amount) }}</td>
                            <td>{{ $campaign->donations_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.donations.campaigns.destroy', $campaign) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this campaign?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-6">{{ $campaigns->links() }}</div>
        @endif
    </div>
</x-layouts::admin>
