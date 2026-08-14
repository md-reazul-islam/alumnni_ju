<x-guest-layout :title="'Sign In'">
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome back</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">Sign in to your alumni account to continue.</p>

    @if (session('status'))
        <x-alert variant="success" class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <x-input label="Email address" name="email" type="email" :value="old('email')" required autofocus autocomplete="username" />

        <div>
            <x-input label="Password" name="password" type="password" required autocomplete="current-password" />
            <div class="mt-2 text-right">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-navy-700 hover:text-navy-900 dark:text-navy-300 dark:hover:text-white">
                        Forgot your password?
                    </a>
                @endif
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
            Remember me
        </label>

        <x-button type="submit" class="w-full">Sign In</x-button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
        Not part of the network yet?
        <a href="{{ route('register') }}" class="font-semibold text-navy-700 hover:text-navy-900 dark:text-navy-300 dark:hover:text-white">Join the Alumni Network</a>
    </p>
</x-guest-layout>
