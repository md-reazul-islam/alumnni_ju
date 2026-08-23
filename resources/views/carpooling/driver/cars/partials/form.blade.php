@php $car ??= null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Make" name="make" :value="old('make', $car?->make)" required />
    <x-input label="Model" name="model" :value="old('model', $car?->model)" required />
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
    <x-input label="Year" name="year" type="number" :value="old('year', $car?->year)" />
    <x-input label="Color" name="color" :value="old('color', $car?->color)" />
    <x-input label="Plate Number" name="plate_number" :value="old('plate_number', $car?->plate_number)" required />
</div>

<x-input label="Passenger Seats" name="total_seats" type="number" min="1" max="8" :value="old('total_seats', $car?->total_seats)" required hint="How many seats besides the driver's — this is the maximum you can offer per trip." />

<div>
    <label class="form-label">Photo</label>
    @if ($car?->photo)
        <img src="{{ asset('storage/' . $car->photo) }}" class="mb-3 h-24 w-auto rounded-lg object-cover">
    @endif
    <input type="file" name="photo" accept="image/*" class="form-input">
    @error('photo')
        <p class="form-error">{{ $message }}</p>
    @enderror
</div>
