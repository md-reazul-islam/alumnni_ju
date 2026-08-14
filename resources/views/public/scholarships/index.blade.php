<x-layouts::app>
    <div class="section-container py-12">
        <x-breadcrumb :items="[['label' => 'Scholarships']]" class="mb-4" />

        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Scholarships</h1>
        <p class="mt-1.5 text-slate-500 dark:text-slate-400">Financial support opportunities for current and prospective students.</p>

        @if ($scholarships->isEmpty())
            <x-empty-state icon="award" title="No scholarships currently open" description="New scholarship opportunities are posted regularly — check back soon." class="mt-8" />
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($scholarships as $scholarship)
                    <a href="{{ route('scholarships.show', $scholarship) }}" class="card card-body transition hover:shadow-popover">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold-50 text-gold-600 dark:bg-navy-800 dark:text-gold-300">
                            <x-icon name="award" class="h-5 w-5" />
                        </span>
                        <p class="mt-4 font-semibold text-slate-900 dark:text-white">{{ $scholarship->name }}</p>
                        @if ($scholarship->amount)
                            <p class="mt-1 text-sm font-medium text-emerald-600">{{ $scholarship->currency }} {{ number_format((float) $scholarship->amount) }}</p>
                        @endif
                        @if ($scholarship->deadline)
                            <p class="mt-2 flex items-center gap-1.5 text-xs text-slate-400"><x-icon name="clock" class="h-3.5 w-3.5" /> Deadline {{ $scholarship->deadline->format('M j, Y') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $scholarships->links() }}</div>
        @endif
    </div>
</x-layouts::app>
