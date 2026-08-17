<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="relative bg-navy-950 font-sans antialiased">
    <div class="pointer-events-none fixed inset-0 z-0" x-data="networkBackground()" x-init="start()">
        <canvas x-ref="networkCanvas" class="h-full w-full opacity-50"></canvas>
    </div>

    <div class="relative z-10">
        <x-site-header />

        <main class="mx-3 my-6 overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-navy-900 sm:mx-6 sm:my-10 lg:mx-10">
            {{ $slot }}
        </main>

        <x-site-footer />
    </div>

    <x-command-palette />

    @stack('scripts')
</body>
</html>
