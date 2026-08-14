<x-layouts::app>
    <div class="section-container max-w-3xl py-12">
        <x-breadcrumb :items="[['label' => 'Scholarships', 'url' => route('scholarships.index')], ['label' => $scholarship->name]]" class="mb-6" />

        <div class="card card-body">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $scholarship->name }}</h1>

            <div class="mt-3 flex flex-wrap gap-3">
                @if ($scholarship->amount)
                    <x-badge variant="success">{{ $scholarship->currency }} {{ number_format((float) $scholarship->amount) }}</x-badge>
                @endif
                @if ($scholarship->deadline)
                    <x-badge variant="warning">Deadline {{ $scholarship->deadline->format('F j, Y') }}</x-badge>
                @endif
            </div>

            <div class="prose prose-slate mt-6 max-w-none dark:prose-invert">
                <h2>Description</h2>
                <p class="whitespace-pre-line">{{ $scholarship->description }}</p>

                @if ($scholarship->eligibility)
                    <h2>Eligibility</h2>
                    <p class="whitespace-pre-line">{{ $scholarship->eligibility }}</p>
                @endif

                @if ($scholarship->required_documents)
                    <h2>Required Documents</h2>
                    <p class="whitespace-pre-line">{{ $scholarship->required_documents }}</p>
                @endif
            </div>

            @if ($scholarship->application_url)
                <x-button :href="$scholarship->application_url" class="mt-6">Apply Now</x-button>
            @endif
        </div>
    </div>
</x-layouts::app>
