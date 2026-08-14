<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-white font-sans antialiased dark:bg-navy-950">
    <x-site-header />

    <main>
        {{ $slot }}
    </main>

    <x-site-footer />

    @stack('scripts')
</body>
</html>
