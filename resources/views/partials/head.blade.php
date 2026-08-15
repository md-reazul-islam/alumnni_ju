@php
    $siteTitle = \App\Models\Setting::get('general', 'site_title', config('app.name'));
    $favicon = \App\Models\Setting::get('general', 'favicon');
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ isset($title) ? $title . ' — ' . $siteTitle : $siteTitle }}</title>

@if ($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
@endif

<script>
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
