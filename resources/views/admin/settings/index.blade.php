<x-layouts::admin :title="'Settings'">
    <div x-data="{ tab: '{{ $errors->general->any() ? 'general' : ($errors->association->any() ? 'association' : ($errors->about->any() ? 'about' : ($errors->login->any() ? 'login' : ($errors->homepage->any() ? 'homepage' : ($errors->navbar->any() ? 'navbar' : session('active_tab', 'institution')))))) }}' }">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Settings</h1>

        @if (session('status'))
            <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
        @endif

        <div class="mt-6 flex gap-1 border-b border-slate-200 dark:border-navy-800">
            <button @click="tab = 'general'" :class="tab === 'general' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">General</button>
            <button @click="tab = 'institution'" :class="tab === 'institution' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Institution</button>
            <button @click="tab = 'association'" :class="tab === 'association' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Alumni Association</button>
            <button @click="tab = 'about'" :class="tab === 'about' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">About Page</button>
            <button @click="tab = 'login'" :class="tab === 'login' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Login Page</button>
            <button @click="tab = 'homepage'" :class="tab === 'homepage' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Homepage Sections</button>
            <button @click="tab = 'navbar'" :class="tab === 'navbar' ? 'border-navy-700 text-navy-800 dark:text-white' : 'border-transparent text-slate-500'" class="border-b-2 px-4 py-2.5 text-sm font-medium">Navbar Menu</button>
        </div>

        <div x-show="tab === 'general'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.general') }}" enctype="multipart/form-data" class="card card-body space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-input label="Site Text" name="site_text" bag="general" hint="Shown next to the logo across the site." :value="$errors->general->any() ? old('site_text') : $general['site_text']" />
                    <x-input label="Website Title" name="site_title" bag="general" hint="Used in the browser tab / page title." :value="$errors->general->any() ? old('site_title') : $general['site_title']" />
                </div>

                <x-textarea label="Footer Tagline" name="footer_tagline" bag="general" rows="2">{{ $errors->general->any() ? old('footer_tagline') : $general['footer_tagline'] }}</x-textarea>
                <p class="form-hint -mt-3">Shown in the footer below the site name. Leave blank to restore the default text.</p>

                @foreach ([
                    'logo' => 'Logo — shown in the header instead of the icon box.',
                    'icon' => 'Icon — shown in the small badge when no logo is set.',
                    'favicon' => 'Favicon — the browser tab icon.',
                ] as $field => $hint)
                    <div class="border-t border-slate-100 pt-5 dark:border-navy-800">
                        <label class="form-label">{{ ucfirst($field) }}</label>
                        <p class="form-hint mb-2">{{ $hint }}</p>

                        @if ($general[$field])
                            <div class="mb-3 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $general[$field]) }}" class="h-12 w-12 rounded-lg border border-slate-200 object-contain dark:border-navy-700">
                                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                    <input type="checkbox" name="remove_{{ $field }}" value="1" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                                    Remove current {{ $field }}
                                </label>
                            </div>
                        @endif

                        <input type="file" name="{{ $field }}" accept="image/*" class="form-input">
                        @error($field, 'general')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="flex justify-end"><x-button type="submit">Save General Settings</x-button></div>
            </form>
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

                <x-textarea label="Contact Page Message" name="contact_message" rows="2" bag="institution">{{ $errors->institution->any() ? old('contact_message') : $institution['contact_message'] }}</x-textarea>
                <p class="form-hint -mt-3">Shown on the Contact page below "Get in Touch". Leave blank to restore the default text.</p>

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

        <div x-show="tab === 'about'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.about') }}" class="card card-body space-y-5">
                @csrf @method('PUT')

                <x-input label="Hero Title" name="hero_title" bag="about" :value="$errors->about->any() ? old('hero_title') : $about['hero_title']" />
                <p class="form-hint -mt-3">The big heading at the top of the About page. Leave blank to restore the default ("About {{ config('app.name') }}").</p>

                <x-textarea label="Hero Subtitle" name="hero_subtitle" bag="about" rows="2">{{ $errors->about->any() ? old('hero_subtitle') : $about['hero_subtitle'] }}</x-textarea>
                <p class="form-hint -mt-3">Shown under the hero title at the top of the page.</p>

                <div class="border-t border-slate-100 pt-5 dark:border-navy-800">
                    <x-input label="Mission Heading" name="mission_heading" bag="about" :value="$errors->about->any() ? old('mission_heading') : $about['mission_heading']" />
                    <x-textarea label="Mission Text" name="mission_text" bag="about" rows="4" class="mt-5">{{ $errors->about->any() ? old('mission_text') : $about['mission_text'] }}</x-textarea>
                </div>

                <div class="border-t border-slate-100 pt-5 dark:border-navy-800" x-data="{ items: @js($errors->about->any() ? old('items', $about['items']) : $about['items']) }">
                    <x-input label="'What We Do' Heading" name="items_heading" bag="about" :value="$errors->about->any() ? old('items_heading') : $about['items_heading']" />

                    <label class="form-label mt-5 block">Items</label>
                    <p class="form-hint mb-3">Each item pairs an icon with a line of text. Add, edit, or remove items as needed.</p>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex flex-col gap-3 rounded-lg border border-slate-200 p-4 dark:border-navy-800 sm:flex-row sm:items-start">
                                <select :name="'items[' + index + '][icon]'" x-model="item.icon" class="form-select sm:w-44">
                                    @foreach (\App\Http\Controllers\Admin\SettingsController::ABOUT_ICON_OPTIONS as $icon)
                                        <option value="{{ $icon }}">{{ $icon }}</option>
                                    @endforeach
                                </select>
                                <textarea :name="'items[' + index + '][text]'" x-model="item.text" rows="2" class="form-textarea flex-1" placeholder="Item text"></textarea>
                                <button type="button" @click="items.splice(index, 1)" class="text-slate-400 hover:text-red-500" title="Remove item">
                                    <x-icon name="trash-2" class="h-5 w-5" />
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="items.push({ icon: 'star', text: '' })" class="btn-secondary mt-3">+ Add Item</button>
                </div>

                <div class="border-t border-slate-100 pt-5 dark:border-navy-800 space-y-5">
                    <x-input label="CTA Heading" name="cta_heading" bag="about" :value="$errors->about->any() ? old('cta_heading') : $about['cta_heading']" />
                    <x-textarea label="CTA Text" name="cta_text" bag="about" rows="2">{{ $errors->about->any() ? old('cta_text') : $about['cta_text'] }}</x-textarea>
                    <x-input label="CTA Button Text" name="cta_button_text" bag="about" :value="$errors->about->any() ? old('cta_button_text') : $about['cta_button_text']" />
                </div>

                <div class="flex justify-end"><x-button type="submit">Save About Page Settings</x-button></div>
            </form>
        </div>

        <div x-show="tab === 'login'" x-cloak class="mt-6">
            <form method="POST" action="{{ route('admin.settings.login-page') }}" class="card card-body space-y-5">
                @csrf @method('PUT')

                <x-input label="Hero Title" name="hero_title" bag="login" :value="$errors->login->any() ? old('hero_title') : $login['hero_title']" />
                <p class="form-hint -mt-3">The big heading on the left panel of the login, register, and password pages. Leave blank to restore the default.</p>

                <x-textarea label="Hero Subtitle" name="hero_subtitle" bag="login" rows="2">{{ $errors->login->any() ? old('hero_subtitle') : $login['hero_subtitle'] }}</x-textarea>
                <p class="form-hint -mt-3">Shown under the hero title on the same panel.</p>

                <p class="form-hint">The "Verified Alumni", "Countries", and "Annual Events" numbers below it are calculated automatically from platform data and aren't editable here.</p>

                <div class="flex justify-end"><x-button type="submit">Save Login Page Settings</x-button></div>
            </form>
        </div>

        <div x-show="tab === 'homepage'" x-cloak class="mt-6">
            <form
                method="POST"
                action="{{ route('admin.settings.homepage') }}"
                class="card card-body"
                x-data="{
                    syncOrder(el) {
                        const keys = Array.from(el.closest('form').querySelectorAll('[data-section-row]')).map(row => row.dataset.sectionKey);
                        el.closest('form').querySelector('input[name=section_order]').value = JSON.stringify(keys);
                    },
                    moveUp(el) {
                        const row = el.closest('[data-section-row]');
                        const prev = row.previousElementSibling;
                        if (prev) row.parentNode.insertBefore(row, prev);
                        this.syncOrder(el);
                    },
                    moveDown(el) {
                        const row = el.closest('[data-section-row]');
                        const next = row.nextElementSibling;
                        if (next) row.parentNode.insertBefore(next, row);
                        this.syncOrder(el);
                    },
                }"
                x-init="syncOrder($el)"
            >
                @csrf @method('PUT')
                <input type="hidden" name="section_order" value="">

                <p class="text-sm text-slate-500 dark:text-slate-400">Turn a section off to hide it from the homepage immediately. Use the arrows to change the order sections appear in — changes apply immediately.</p>

                <div class="mt-4 divide-y divide-slate-100 dark:divide-navy-800">
                    @foreach ($homepageOrder as $key)
                        <div data-section-row data-section-key="{{ $key }}" class="flex items-center gap-3 py-1">
                            <div class="flex flex-shrink-0 flex-col">
                                <button type="button" @click="moveUp($event.target)" class="text-slate-400 hover:text-navy-700 disabled:opacity-30 dark:hover:text-white">
                                    <x-icon name="chevron-up" class="h-4 w-4" />
                                </button>
                                <button type="button" @click="moveDown($event.target)" class="text-slate-400 hover:text-navy-700 disabled:opacity-30 dark:hover:text-white">
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </button>
                            </div>
                            <div class="min-w-0 flex-1">
                                <x-toggle :name="$key" :label="\App\Http\Controllers\Admin\SettingsController::HOMEPAGE_SECTIONS[$key]" :checked="$homepage[$key]" />
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-end"><x-button type="submit">Save Homepage Sections</x-button></div>
            </form>
        </div>

        <div x-show="tab === 'navbar'" x-cloak class="mt-6">
            <form
                method="POST"
                action="{{ route('admin.settings.navbar') }}"
                class="card card-body"
                x-data="{
                    syncOrder(el) {
                        const keys = Array.from(el.closest('form').querySelectorAll('[data-nav-row]')).map(row => row.dataset.navKey);
                        el.closest('form').querySelector('input[name=menu_order]').value = JSON.stringify(keys);
                    },
                    moveUp(el) {
                        const row = el.closest('[data-nav-row]');
                        const prev = row.previousElementSibling;
                        if (prev) row.parentNode.insertBefore(row, prev);
                        this.syncOrder(el);
                    },
                    moveDown(el) {
                        const row = el.closest('[data-nav-row]');
                        const next = row.nextElementSibling;
                        if (next) row.parentNode.insertBefore(next, row);
                        this.syncOrder(el);
                    },
                }"
                x-init="syncOrder($el)"
            >
                @csrf @method('PUT')
                <input type="hidden" name="menu_order" value="">

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Turn "Show outside dropdown" on to pin a menu to the main navbar; leave it off to keep it inside the "More" dropdown. Use the arrows to set the order — it applies to both the main navbar and the dropdown. Changes apply immediately.
                </p>

                <div class="mt-4 divide-y divide-slate-100 dark:divide-navy-800">
                    @foreach ($navbarOrder as $key)
                        @php $item = \App\Http\Controllers\Admin\SettingsController::NAVBAR_MENU_ITEMS[$key]; @endphp
                        <div data-nav-row data-nav-key="{{ $key }}" class="flex items-center gap-3 py-1">
                            <div class="flex flex-shrink-0 flex-col">
                                <button type="button" @click="moveUp($event.target)" class="text-slate-400 hover:text-navy-700 disabled:opacity-30 dark:hover:text-white">
                                    <x-icon name="chevron-up" class="h-4 w-4" />
                                </button>
                                <button type="button" @click="moveDown($event.target)" class="text-slate-400 hover:text-navy-700 disabled:opacity-30 dark:hover:text-white">
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </button>
                            </div>
                            <div class="min-w-0 flex-1">
                                <x-toggle
                                    name="primary_{{ $key }}"
                                    :label="$item['label']"
                                    :hint="'Dropdown group when hidden: ' . $item['group']"
                                    :checked="in_array($key, $navbarPrimaryKeys, true)"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-end"><x-button type="submit">Save Navbar Menu</x-button></div>
            </form>
        </div>
    </div>
</x-layouts::admin>
