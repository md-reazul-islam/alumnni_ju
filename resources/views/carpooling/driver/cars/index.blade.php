<x-layouts::alumni :title="'My Cars'">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">My Cars</h1>
        <x-button :href="route('carpooling.cars.create')" size="sm"><x-icon name="plus" class="h-4 w-4" /> Add a Car</x-button>
    </div>

    @if (session('status'))
        <x-alert variant="success" class="mt-4">{{ session('status') }}</x-alert>
    @endif

    @if ($cars->isEmpty())
        <x-empty-state icon="car" title="You haven't added any cars yet" class="mt-8" />
    @else
        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($cars as $car)
                <div class="card overflow-hidden">
                    <div class="flex h-32 items-center justify-center bg-navy-100 text-navy-400 dark:bg-navy-800">
                        @if ($car->photo)
                            <img src="{{ asset('storage/' . $car->photo) }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="car" class="h-10 w-10" />
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $car->display_name }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $car->color }} &middot; {{ $car->plate_number }}</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $car->total_seats }} passenger seats</p>

                        <div class="mt-3 flex items-center gap-3">
                            <a href="{{ route('carpooling.cars.edit', $car) }}" class="text-sm font-medium text-navy-700 hover:underline dark:text-navy-300">Edit</a>
                            <form method="POST" action="{{ route('carpooling.cars.destroy', $car) }}" onsubmit="event.preventDefault(); Swal.fire({title:'Remove this car?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Remove'}).then(r=>r.isConfirmed&&this.submit())">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts::alumni>
