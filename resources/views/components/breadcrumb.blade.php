@props(['items' => [], 'onDark' => false])

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm {{ $onDark ? 'text-navy-300' : 'text-slate-500 dark:text-slate-400' }}">
        <li class="flex items-center gap-1.5">
            <a href="{{ route('home') }}" class="{{ $onDark ? 'hover:text-white' : 'hover:text-navy-700 dark:hover:text-white' }}">
                <x-icon name="house" class="h-4 w-4" />
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 {{ $onDark ? 'text-navy-700' : 'text-slate-300 dark:text-navy-700' }}" />

                @if (!$loop->last && isset($item['url']))
                    <a href="{{ $item['url'] }}" class="{{ $onDark ? 'hover:text-white' : 'hover:text-navy-700 dark:hover:text-white' }}">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium {{ $onDark ? 'text-white' : 'text-slate-700 dark:text-slate-200' }}">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
