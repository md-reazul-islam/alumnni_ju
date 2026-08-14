<x-guest-layout :title="'Reset Password'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Reset your password</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Choose a new password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input label="Email address" name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
        <x-input label="New password" name="password" type="password" required autocomplete="new-password" />
        <x-input label="Confirm password" name="password_confirmation" type="password" required autocomplete="new-password" />

        <x-button type="submit" class="w-full">Reset Password</x-button>
    </form>
</x-guest-layout>
