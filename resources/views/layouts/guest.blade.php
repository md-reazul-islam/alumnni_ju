@php
    $loginHeroTitle = \App\Models\Setting::get('login', 'hero_title', \App\Http\Controllers\Admin\SettingsController::DEFAULT_LOGIN_HERO_TITLE);
    $loginHeroSubtitle = \App\Models\Setting::get('login', 'hero_subtitle', \App\Http\Controllers\Admin\SettingsController::DEFAULT_LOGIN_HERO_SUBTITLE);
    $loginStats = \Illuminate\Support\Facades\Cache::remember('login.stats', now()->addMinutes(15), function () {
        return [
            'verified_alumni' => \App\Models\User::verified()->whereHas('role', fn ($q) => $q->where('slug', 'alumni'))->count(),
            'countries' => \App\Models\AlumniProfile::whereNotNull('country')->distinct('country')->count('country'),
            'annual_events' => \App\Models\Event::published()->whereYear('event_date', now()->year)->count(),
        ];
    });
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden flex-col justify-between overflow-hidden bg-navy-950 p-12 text-white lg:flex">
            <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 28px 28px;"></div>

            <a href="{{ route('home') }}" class="relative flex items-center gap-2.5 text-lg font-bold">
                <x-brand-icon box-class="bg-gold-500 text-navy-950" box-size="h-9 w-9" icon-size="h-5 w-5" logo-height="h-9" />
                {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
            </a>

            <div class="relative max-w-md">
                <p class="text-3xl font-bold leading-tight">{{ $loginHeroTitle }}</p>
                <p class="mt-4 text-navy-200">
                    {{ $loginHeroSubtitle }}
                </p>

                <div class="mt-10 flex gap-8 border-t border-white/10 pt-8">
                    <div>
                        <p class="text-2xl font-bold text-gold-400">{{ number_format($loginStats['verified_alumni']) }}</p>
                        <p class="text-sm text-navy-300">Verified Alumni</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gold-400">{{ number_format($loginStats['countries']) }}</p>
                        <p class="text-sm text-navy-300">Countries</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gold-400">{{ number_format($loginStats['annual_events']) }}</p>
                        <p class="text-sm text-navy-300">Annual Events</p>
                    </div>
                </div>
            </div>

            <p class="relative text-sm text-navy-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

        <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="mx-auto w-full max-w-sm">
                <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2.5 text-lg font-bold text-navy-900 lg:hidden dark:text-white">
                    <x-brand-icon box-size="h-9 w-9" icon-size="h-5 w-5" logo-height="h-9" />
                    {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
                </a>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
