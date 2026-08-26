<x-layouts::admin :title="'Customer Feedback'">
    <x-breadcrumb :items="[['label' => 'Catering'], ['label' => 'Reports', 'url' => route('admin.catering.reports.index')], ['label' => 'Customer Feedback']]" class="mb-4" />
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Customer Feedback</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Average rating: {{ number_format($averageRating, 1) }} / 5</p>
        </div>
        <a href="{{ route('admin.catering.reports.export', 'feedback') }}" class="btn-secondary btn-sm">Export CSV</a>
    </div>

    @if ($feedback->isEmpty())
        <x-empty-state icon="star" title="No feedback yet" class="mt-8" />
    @else
        <div class="mt-6 space-y-4">
            @foreach ($feedback as $fb)
                <div class="card card-body">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $fb->customer->full_name }}</p>
                            <p class="text-xs text-slate-400">{{ $fb->order?->category?->name }} &middot; {{ $fb->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $fb->rating ? 'text-amber-400' : 'text-slate-200 dark:text-navy-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.54 1.118l-3.367-2.446a1 1 0 00-1.175 0l-3.367 2.446c-.784.57-1.838-.196-1.539-1.118l1.286-3.957a1 1 0 00-.363-1.118L2.05 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if ($fb->comment)
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $fb->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $feedback->links() }}</div>
    @endif
</x-layouts::admin>
