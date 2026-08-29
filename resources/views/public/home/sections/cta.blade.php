<section class="overflow-hidden rounded-3xl bg-navy-900 shadow-xl">
    <div class="section-container flex flex-col items-center justify-between gap-6 py-8 text-center sm:flex-row sm:text-left">
        <div>
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Your university journey continues.</h2>
            <p class="mt-2 text-navy-200">Update your profile and stay connected with your alma mater.</p>
        </div>
        <div class="flex flex-shrink-0 flex-col gap-3 sm:flex-row">
            <x-button :href="route('register')" variant="gold">Become a {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }} Member</x-button>
            @auth
                <x-button :href="route('profile.edit')" variant="secondary" class="bg-white/10 text-white ring-white/20 hover:bg-white/20">Update Your Profile</x-button>
            @else
                <x-button :href="route('login')" variant="secondary" class="bg-white/10 text-white ring-white/20 hover:bg-white/20">Update Your Profile</x-button>
            @endauth
        </div>
    </div>
</section>
