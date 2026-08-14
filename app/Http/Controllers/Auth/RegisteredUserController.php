<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterAlumniRequest;
use App\Models\Campus;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Interest;
use App\Services\AlumniRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $formOptions = Cache::remember('registration.form-options', now()->addHour(), function () {
            return [
                'departments' => Department::with('programs')->orderBy('name')->get(),
                'degrees' => Degree::orderBy('name')->get(),
                'campuses' => Campus::orderBy('name')->get(),
                'interests' => Interest::orderBy('name')->get(),
            ];
        });

        return view('auth.register', $formOptions);
    }

    public function store(RegisterAlumniRequest $request, AlumniRegistrationService $registrationService): RedirectResponse
    {
        $user = $registrationService->register($request->validated(), $request->file('photo'));

        Auth::login($user);

        return redirect()->route('verification.pending');
    }
}
