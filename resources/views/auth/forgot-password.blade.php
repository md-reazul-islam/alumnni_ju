<x-guest-layout :title="'Forgot Password'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Forgot your password?</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
        No problem. Enter your email address and we'll send you a link to reset it.
    </p>

    @if (session('status'))
        <x-alert variant="success" class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <x-input label="Email address" name="email" type="email" :value="old('email')" required autofocus />

        <x-button type="submit" class="w-full">Email Password Reset Link</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
        <a href="{{ route('login') }}" class="font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 dark:hover:text-white">Back to sign in</a>
    </p>
</x-guest-layout>
