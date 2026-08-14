@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
        <li class="flex items-center gap-1.5">
            <a href="{{ route('home') }}" class="hover:text-navy-700 dark:hover:text-white">
                <x-icon name="house" class="h-4 w-4" />
            </a>
        </li>

        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                <x-icon name="chevron-right" class="h-3.5 w-3.5 text-slate-300 dark:text-navy-700" />

                @if (!$loop->last && isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-navy-700 dark:hover:text-white">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium text-slate-700 dark:text-slate-200">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
