<x-layouts::admin :title="'Settings'">
    <div x-data="{ tab: 'institution' }">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>

        <div class="mt-6 flex gap-1 border-b border-slate-200 dark:border-navy-800">
            <button @click="tab = 'institution'" :class="tab === 'institution' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Institution</button>
            <button @click="tab = 'association'" :class="tab === 'association' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Alumni Association</button>
        </div>

        <div x-show="tab === 'institution'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.institution') }}" class="card card-body space-y-5">
                @csrf @method('PUT')
                <x-input label="University Name" name="name" :value="$institution['name']" required />
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Email" name="email" type="email" :value="$institution['email']" />
                    <x-input label="Phone" name="phone" :value="$institution['phone']" />
                </div>
                <x-input label="Website" name="website" type="url" :value="$institution['website']" />
                <x-textarea label="Address" name="address" rows="2">{{ $institution['address'] }}</x-textarea>
                <div class="flex justify-end"><x-button type="submit">Save Institution Settings</x-button></div>
            </form>
        </div>

        <div x-show="tab === 'association'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.association') }}" class="card card-body space-y-5">
                @csrf @method('PUT')
                <x-input label="Association Name" name="name" :value="$association['name']" />
                <x-textarea label="Description" name="description" rows="3">{{ $association['description'] }}</x-textarea>
                <x-input label="Contact Email" name="contact_email" type="email" :value="$association['contact_email']" />
                <div class="flex justify-end"><x-button type="submit">Save Association Settings</x-button></div>
            </form>
        </div>
    </div>
</x-layouts::admin>
