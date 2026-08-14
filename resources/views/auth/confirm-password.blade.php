<x-guest-layout :title="'Confirm Password'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Confirm your password</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
        This is a secure area. Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        <x-input label="Password" name="password" type="password" required autocomplete="current-password" />

        <x-button type="submit" class="w-full">Confirm</x-button>
    </form>
</x-guest-layout>
