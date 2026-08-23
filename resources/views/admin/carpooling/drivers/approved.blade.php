<x-layouts::admin :title="'Approved Drivers'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Approved Drivers']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Approved Drivers</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($profiles->isEmpty())
        <x-empty-state icon="car" title="No approved drivers yet" class="mt-8" />
    @else
        <x-table class="mt-6">
            <thead><tr><th>Driver</th><th>License #</th><th>Cars</th><th>Total Earned</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @foreach ($profiles as $profile)
                    <tr>
                        <td class="font-medium text-slate-900 dark:text-white">
                            <a href="{{ route('admin.carpooling.drivers.show', $profile) }}" class="hover:underline">{{ $profile->user->full_name }}</a>
                        </td>
                        <td>{{ $profile->license_number }}</td>
                        <td>{{ $profile->cars_count }}</td>
                        <td>${{ number_format($profile->total_earned, 2) }}</td>
                        <td><x-badge :variant="$profile->is_active ? 'success' : 'neutral'">{{ $profile->is_active ? 'Active' : 'Paused' }}</x-badge></td>
                        <td>
                            <form method="POST" action="{{ route('admin.carpooling.drivers.suspend', $profile) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Suspend this driver?',input:'text',inputLabel:'Reason',inputPlaceholder:'Reason for suspension',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Suspend'}).then(r=>{ if(r.isConfirmed && r.value){ this.querySelector('[name=rejection_reason]').value = r.value; this.submit(); } })">
                                @csrf
                                <input type="hidden" name="rejection_reason" value="">
                                <button type="submit" class="text-slate-400 hover:text-red-600">Suspend</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="mt-6">{{ $profiles->links() }}</div>
    @endif
</x-layouts::admin>
