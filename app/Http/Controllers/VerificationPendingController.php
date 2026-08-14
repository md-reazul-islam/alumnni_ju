<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationPendingController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('auth.verification-pending', ['user' => $request->user()]);
    }
}
