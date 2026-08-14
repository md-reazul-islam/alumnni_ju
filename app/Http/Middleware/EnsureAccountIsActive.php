<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === User::STATUS_SUSPENDED) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been suspended. Please contact the alumni office for assistance.',
            ]);
        }

        if ($user && $user->status === User::STATUS_REJECTED) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your alumni verification was not approved. Please contact the alumni office.',
            ]);
        }

        $isExemptRoute = $request->routeIs(
            'verification.pending',
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout',
            'profile.*'
        );

        if ($user && $user->status === User::STATUS_PENDING && ! $user->isAdminStaff() && ! $isExemptRoute) {
            return redirect()->route('verification.pending');
        }

        return $next($request);
    }
}
