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
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gold-500 text-navy-950">
                    <x-icon name="graduation-cap" class="h-5 w-5" />
                </span>
                {{ config('app.name') }}
            </a>

            <div class="relative max-w-md">
                <p class="text-3xl font-bold leading-tight">Connect. Engage. Inspire. Give Back.</p>
                <p class="mt-4 text-navy-200">
                    Reconnect with your university community and build meaningful professional relationships
                    with alumni around the world.
                </p>

                <div class="mt-10 flex gap-8 border-t border-white/10 pt-8">
                    <div>
                        <p class="text-2xl font-bold text-gold-400">12,000+</p>
                        <p class="text-sm text-navy-300">Verified Alumni</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gold-400">80+</p>
                        <p class="text-sm text-navy-300">Countries</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gold-400">150+</p>
                        <p class="text-sm text-navy-300">Annual Events</p>
                    </div>
                </div>
            </div>

            <p class="relative text-sm text-navy-400">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>

        <div class="flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="mx-auto w-full max-w-sm">
                <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2.5 text-lg font-bold text-navy-900 lg:hidden dark:text-white">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-navy-800 text-gold-400">
                        <x-icon name="graduation-cap" class="h-5 w-5" />
                    </span>
                    {{ config('app.name') }}
                </a>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
