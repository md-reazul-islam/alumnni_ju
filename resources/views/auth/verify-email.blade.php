<x-guest-layout :title="'Verify Email'">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-50 text-navy-700 dark:bg-navy-800 dark:text-navy-200">
        <x-icon name="mail" class="h-6 w-6" />
    </div>

    <h1 class="mt-5 text-2xl font-bold text-slate-900 dark:text-white">Verify your email</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
        Thanks for signing up! Please verify your email address by clicking the link we just emailed you.
        Didn't get it? We can send another.
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-alert variant="success" class="mt-6">
            A new verification link has been sent to the email address you provided during registration.
        </x-alert>
    @endif

    <div class="mt-8 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="submit">Resend Verification Email</x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-800 dark:hover:text-white">Log Out</button>
        </form>
    </div>
</x-guest-layout>
