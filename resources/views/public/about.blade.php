<x-layouts::app>
    @php
        $aboutItemsRaw = \App\Models\Setting::get('about', 'items');
        $aboutItems = $aboutItemsRaw ? json_decode($aboutItemsRaw, true) : \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_ITEMS;
    @endphp

    <section class="overflow-hidden rounded-3xl bg-navy-950 py-10 text-center text-white shadow-xl sm:py-14">
        <div class="section-container">
            <h1 class="text-3xl font-bold sm:text-4xl">{{ \App\Models\Setting::get('about', 'hero_title', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_HERO_TITLE_PREFIX . config('app.name')) }}</h1>
            <p class="mx-auto mt-3 max-w-2xl text-navy-200">
                {{ \App\Models\Setting::get('about', 'hero_subtitle', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_HERO_SUBTITLE) }}
            </p>
        </div>
    </section>

    <section>
      <div class="section-container grid grid-cols-1 gap-10 py-10 sm:py-14 lg:grid-cols-2">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ \App\Models\Setting::get('about', 'mission_heading', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_MISSION_HEADING) }}</h2>
            <p class="mt-3 text-navy-200">
                {{ \App\Models\Setting::get('about', 'mission_text', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_MISSION_TEXT) }}
            </p>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-white">{{ \App\Models\Setting::get('about', 'items_heading', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_ITEMS_HEADING) }}</h2>
            <ul class="mt-3 space-y-3 text-navy-200">
                @foreach ($aboutItems as $item)
                    <li class="flex gap-3"><x-icon :name="$item['icon']" class="mt-0.5 h-5 w-5 flex-shrink-0 text-gold-400" /> {{ $item['text'] }}</li>
                @endforeach
            </ul>
        </div>
      </div>
    </section>

    <section>
        <div class="section-container py-10 text-center sm:py-14">
            <h2 class="text-2xl font-bold text-white">{{ \App\Models\Setting::get('about', 'cta_heading', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_CTA_HEADING) }}</h2>
            <p class="mt-2 text-navy-200">{{ \App\Models\Setting::get('about', 'cta_text', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_CTA_TEXT) }}</p>
            <x-button :href="route('register')" variant="gold" class="mt-6">{{ \App\Models\Setting::get('about', 'cta_button_text', \App\Http\Controllers\Admin\SettingsController::DEFAULT_ABOUT_CTA_BUTTON_TEXT) }}</x-button>
        </div>
    </section>
</x-layouts::app>
