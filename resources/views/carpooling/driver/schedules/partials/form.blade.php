@php $schedule ??= null; @endphp

<x-select
    label="Car"
    name="carpool_car_id"
    :options="$cars->pluck('display_name', 'id')"
    :selected="$schedule?->carpool_car_id"
    placeholder="Select a car"
    required
/>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Origin" name="origin" :value="old('origin', $schedule?->origin)" required />
    <x-input label="Destination" name="destination" :value="old('destination', $schedule?->destination)" required />
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Departure Date" name="departure_date" type="date" :value="old('departure_date', $schedule?->departure_date?->format('Y-m-d'))" required />
    <x-input label="Departure Time" name="departure_time" type="time" :value="old('departure_time', $schedule ? substr($schedule->departure_time, 0, 5) : null)" required />
</div>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <x-input label="Price Per Seat (USD)" name="price_per_seat" type="number" step="0.01" min="1" :value="old('price_per_seat', $schedule?->price_per_seat)" required hint="Total trip fare = this × seats offered." />
    <x-input label="Seats Offered" name="seats_offered" type="number" min="1" max="8" :value="old('seats_offered', $schedule?->seats_offered)" required />
</div>

<x-textarea label="Notes" name="notes" rows="3">{{ old('notes', $schedule?->notes) }}</x-textarea>

@if ($schedule?->isLocked())
    <x-alert variant="warning">
        This trip already has paid bookings. Car, price, date, and time are locked to protect passengers — only origin, destination, and notes can be edited.
    </x-alert>
@endif
