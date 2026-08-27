<?php

namespace App\Http\Controllers;

use App\Models\CarpoolCar;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CarpoolCarController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->carpoolDriverProfile;
        abort_unless($profile, 404);

        $cars = $profile->cars()->latest()->get();

        return view('carpooling.driver.cars.index', compact('cars'));
    }

    public function create(): View
    {
        $this->authorize('create', CarpoolCar::class);

        return view('carpooling.driver.cars.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', CarpoolCar::class);

        $data = $this->validated($request);
        $data['carpool_driver_profile_id'] = $request->user()->carpoolDriverProfile->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = app(ImageUploadService::class)->store($request->file('photo'), 'carpool-cars', ImageUploadService::MAX_LARGE);
        }

        CarpoolCar::create($data);

        return redirect()->route('carpooling.cars.index')->with('status', 'Car added.');
    }

    public function edit(CarpoolCar $car): View
    {
        $this->authorize('update', $car);

        return view('carpooling.driver.cars.edit', compact('car'));
    }

    public function update(Request $request, CarpoolCar $car): RedirectResponse
    {
        $this->authorize('update', $car);

        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            if ($car->photo) {
                Storage::disk('public')->delete($car->photo);
            }
            $data['photo'] = app(ImageUploadService::class)->store($request->file('photo'), 'carpool-cars', ImageUploadService::MAX_LARGE);
        }

        $car->update($data);

        return redirect()->route('carpooling.cars.index')->with('status', 'Car updated.');
    }

    public function destroy(CarpoolCar $car): RedirectResponse
    {
        $this->authorize('delete', $car);

        abort_if($car->schedules()->exists(), 422, 'Remove or complete this car\'s trips before deleting it.');

        if ($car->photo) {
            Storage::disk('public')->delete($car->photo);
        }

        $car->delete();

        return redirect()->route('carpooling.cars.index')->with('status', 'Car removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'make' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1980', 'max:' . (now()->year + 1)],
            'color' => ['nullable', 'string', 'max:50'],
            'plate_number' => ['required', 'string', 'max:20'],
            'total_seats' => ['required', 'integer', 'min:1', 'max:8'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=400,min_height=300'],
        ]);
    }
}
