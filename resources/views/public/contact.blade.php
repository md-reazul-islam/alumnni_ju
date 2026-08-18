<x-layouts::app>
    <section>
      <div class="section-container grid grid-cols-1 gap-12 py-16 sm:py-20 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <h1 class="text-3xl font-bold text-white">Get in Touch</h1>
            <p class="mt-3 text-navy-200">
                {{ \App\Models\Setting::get('institution', 'contact_message', \App\Http\Controllers\Admin\SettingsController::DEFAULT_CONTACT_MESSAGE) }}
            </p>

            <div class="mt-8 space-y-5">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold-400">
                        <x-icon name="mail" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-white">Email</p>
                        <p class="text-sm text-navy-200">{{ \App\Models\Setting::get('institution', 'email', 'alumni@university.edu') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold-400">
                        <x-icon name="phone" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-white">Phone</p>
                        <p class="text-sm text-navy-200">{{ \App\Models\Setting::get('institution', 'phone', '+1 (555) 010-0100') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold-400">
                        <x-icon name="map-pin" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-white">Address</p>
                        <p class="text-sm text-navy-200">{{ \App\Models\Setting::get('institution', 'address', 'University Alumni Office, Main Campus') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="card card-body">
                @if (session('status'))
                    <x-alert variant="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-input label="Full name" name="name" :value="old('name')" required />
                        <x-input label="Email address" name="email" type="email" :value="old('email')" required />
                    </div>
                    <x-input label="Subject" name="subject" :value="old('subject')" required />
                    <x-textarea label="Message" name="message" rows="6" required>{{ old('message') }}</x-textarea>
                    <x-button type="submit" class="w-full sm:w-auto">Send Message</x-button>
                </form>
            </div>
        </div>
      </div>
    </section>
</x-layouts::app>
