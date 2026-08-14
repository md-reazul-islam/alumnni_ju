<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-slate-50 font-sans antialiased dark:bg-navy-950">
    <x-navbar />

    <main class="section-container py-8">
        @isset($header)
            <div class="mb-6">{{ $header }}</div>
        @endisset

        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
