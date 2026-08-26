<x-layouts::admin :title="'Catering Settings'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Settings']]" class="mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Catering Settings</h1>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('admin.catering.settings.update') }}" class="card card-body mt-6 max-w-lg space-y-5">
        @csrf
        <x-input
            label="Tax (%)"
            name="tax_percentage"
            type="number"
            step="0.01"
            min="0"
            max="50"
            :value="old('tax_percentage', $settings['tax_percentage'])"
            hint="Applied to every priced order's subtotal when generating the invoice."
            required
        />

        <x-input
            label="VAT (%)"
            name="vat_percentage"
            type="number"
            step="0.01"
            min="0"
            max="50"
            :value="old('vat_percentage', $settings['vat_percentage'])"
            hint="Applied to every priced order's subtotal when generating the invoice."
            required
        />

        <x-input
            label="Service Fee (%)"
            name="service_fee_percentage"
            type="number"
            step="0.01"
            min="0"
            max="50"
            :value="old('service_fee_percentage', $settings['service_fee_percentage'])"
            hint="Platform service charge applied to every priced order's subtotal."
            required
        />

        <x-input
            label="Cancellation Window (hours)"
            name="cancellation_window_hours"
            type="number"
            min="0"
            max="720"
            :value="old('cancellation_window_hours', $settings['cancellation_window_hours'])"
            hint="A customer must cancel an accepted order at least this many hours before the event date to get a refund."
            required
        />

        <div class="flex justify-end">
            <x-button type="submit">Save Settings</x-button>
        </div>
    </form>
</x-layouts::admin>
