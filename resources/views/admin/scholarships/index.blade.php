<x-layouts::admin :title="'Scholarships'">
    <div x-data="{ adding: false }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Scholarships</h1>
            <x-button size="sm" @click="adding = !adding"><x-icon name="plus" class="h-4 w-4" /> Add Scholarship</x-button>
        </div>

        <form method="POST" action="{{ route('admin.scholarships.store') }}" x-show="adding" x-cloak class="card card-body mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-input label="Name" name="name" required />
                <x-input label="Amount" name="amount" type="number" />
            </div>
            <x-textarea label="Description" name="description" rows="3" required />
            <x-textarea label="Eligibility" name="eligibility" rows="2" />
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="form-label">Deadline</label>
                    <input type="text" name="deadline" class="form-input flatpickr-deadline" autocomplete="off">
                </div>
                <x-select label="Status" name="status" required :options="['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed']" />
            </div>
            <x-input label="Application URL" name="application_url" type="url" />
            <x-textarea label="Required Documents" name="required_documents" rows="2" />
            <div class="flex justify-end"><x-button type="submit" size="sm">Save</x-button></div>
        </form>

        @if ($scholarships->isEmpty())
            <x-empty-state icon="award" title="No scholarships yet" class="mt-8" />
        @else
            <x-table class="mt-6">
                <thead><tr><th>Name</th><th>Amount</th><th>Deadline</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @foreach ($scholarships as $scholarship)
                        <tr>
                            <td class="font-medium text-slate-900 dark:text-white">{{ $scholarship->name }}</td>
                            <td>{{ $scholarship->amount ? $scholarship->currency . ' ' . number_format((float) $scholarship->amount) : '—' }}</td>
                            <td>{{ $scholarship->deadline?->format('M d, Y') ?? '—' }}</td>
                            <td><x-badge :variant="$scholarship->status === 'open' ? 'success' : 'neutral'">{{ ucfirst($scholarship->status) }}</x-badge></td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-scholarship-{{ $scholarship->id }}' }))" class="text-slate-400 hover:text-navy-700">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>
                                    <form method="POST" action="{{ route('admin.scholarships.destroy', $scholarship) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Delete this scholarship?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Delete'}).then(r=>r.isConfirmed&&this.submit())">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600"><x-icon name="trash-2" class="h-4 w-4" /></button>
                                    </form>
                                </div>

                                <x-modal name="edit-scholarship-{{ $scholarship->id }}">
                                    <form method="POST" action="{{ route('admin.scholarships.update', $scholarship) }}" class="space-y-4 p-6">
                                        @csrf @method('PUT')
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Edit Scholarship</h3>
                                        <x-input label="Name" name="name" :value="$scholarship->name" required />
                                        <x-textarea label="Description" name="description" rows="3" required>{{ $scholarship->description }}</x-textarea>
                                        <div class="grid grid-cols-2 gap-4">
                                            <x-input label="Amount" name="amount" type="number" :value="$scholarship->amount" />
                                            <x-select label="Status" name="status" :selected="$scholarship->status" required :options="['draft' => 'Draft', 'open' => 'Open', 'closed' => 'Closed']" />
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="btn-secondary btn-sm" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-scholarship-{{ $scholarship->id }}' }))">Cancel</button>
                                            <button type="submit" class="btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-6">{{ $scholarships->links() }}</div>
        @endif
    </div>

    @push('scripts')
    <script>flatpickr('.flatpickr-deadline', { dateFormat: 'Y-m-d' });</script>
    @endpush
</x-layouts::admin>
