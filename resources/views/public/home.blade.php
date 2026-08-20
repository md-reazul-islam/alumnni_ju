<x-layouts::app>
    @foreach ($sectionOrder as $sectionKey)
        @if (\App\Models\Setting::get('homepage', $sectionKey, true) !== '0')
            @include('public.home.sections.' . \Illuminate\Support\Str::after($sectionKey, 'show_'))
        @endif
    @endforeach
</x-layouts::app>
