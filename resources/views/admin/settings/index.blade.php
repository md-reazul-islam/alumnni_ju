<x-layouts::admin :title="'Settings'">
    <div x-data="{ tab: '{{ $errors->association->any() ? 'association' : session('active_tab', 'institution') }}' }">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>

        @if (session('status'))
            <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
        @endif

        <div class="mt-6 flex gap-1 border-b border-slate-200 dark:border-navy-800">
            <button @click="tab = 'institution'" :class="tab === 'institution' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Institution</button>
            <button @click="tab = 'association'" :class="tab === 'association' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Alumni Association</button>
        </div>

        <div x-show="tab === 'institution'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.institution') }}" class="card card-body space-y-5">
                @csrf @method('PUT')
                <x-input label="University Name" name="name" bag="institution" :value="$errors->institution->any() ? old('name') : $institution['name']" required />
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Email" name="email" type="email" bag="institution" :value="$errors->institution->any() ? old('email') : $institution['email']" />
                    <x-input label="Phone" name="phone" bag="institution" :value="$errors->institution->any() ? old('phone') : $institution['phone']" />
                </div>
                <x-input label="Website" name="website" type="url" bag="institution" :value="$errors->institution->any() ? old('website') : $institution['website']" />
                <x-textarea label="Address" name="address" rows="2" bag="institution">{{ $errors->institution->any() ? old('address') : $institution['address'] }}</x-textarea>
                <div class="flex justify-end"><x-button type="submit">Save Institution Settings</x-button></div>
            </form>
        </div>

        <div x-show="tab === 'association'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.association') }}" class="card card-body space-y-5">
                @csrf @method('PUT')
                <x-input label="Association Name" name="name" bag="association" :value="$errors->association->any() ? old('name') : $association['name']" />
                <x-textarea label="Description" name="description" rows="3" bag="association">{{ $errors->association->any() ? old('description') : $association['description'] }}</x-textarea>
                <x-input label="Contact Email" name="contact_email" type="email" bag="association" :value="$errors->association->any() ? old('contact_email') : $association['contact_email']" />
                <div class="flex justify-end"><x-button type="submit">Save Association Settings</x-button></div>
            </form>
        </div>
    </div>
</x-layouts::admin>
