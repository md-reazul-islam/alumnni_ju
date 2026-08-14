<x-guest-layout :title="'Verification Pending'">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gold-50 text-gold-600 dark:bg-gold-900/30 dark:text-gold-300">
        <x-icon name="clock" class="h-6 w-6" />
    </div>

    <h1 class="mt-5 text-2xl font-bold text-slate-900 dark:text-white">Your account is under review</h1>
    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
        Thanks for registering, {{ $user->first_name }}. Our alumni office is verifying your academic details.
        You'll receive an email as soon as your account is approved — this usually takes 1&ndash;2 business days.
    </p>

    <x-alert variant="info" class="mt-6">
        Verification status: <span class="font-semibold capitalize">{{ $user->status }}</span>
    </x-alert>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <x-button type="submit" variant="secondary" class="w-full">Log Out</x-button>
    </form>
</x-guest-layout>
