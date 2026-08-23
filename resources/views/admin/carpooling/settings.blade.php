<x-layouts::admin :title="'Carpooling Settings'">
    <x-breadcrumb :items="[['label' => 'Carpooling'], ['label' => 'Settings']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Carpooling Settings</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('admin.carpooling.settings.update') }}" class="card card-body mt-6 max-w-lg space-y-5">
        @csrf
        <x-input
            label="Platform Commission (%)"
            name="commission_percentage"
            type="number"
            step="0.01"
            min="0"
            max="50"
            :value="old('commission_percentage', $settings['commission_percentage'])"
            hint="Percentage of each confirmed trip's fare kept by the platform. The rest is owed to the driver."
            required
        />

        <x-input
            label="Payment Window (minutes)"
            name="payment_window_minutes"
            type="number"
            min="5"
            max="1440"
            :value="old('payment_window_minutes', $settings['payment_window_minutes'])"
            hint="How long a passenger has to pay after a driver accepts their seat request before it expires."
            required
        />

        <x-input
            label="Cancellation Window (hours)"
            name="cancellation_window_hours"
            type="number"
            min="0"
            max="168"
            :value="old('cancellation_window_hours', $settings['cancellation_window_hours'])"
            hint="A passenger must cancel a confirmed booking at least this many hours before departure to get a refund."
            required
        />

        <div class="flex justify-end">
            <x-button type="submit">Save Settings</x-button>
        </div>
    </form>
</x-layouts::admin>
