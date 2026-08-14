<section>
    <header>
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Profile Information</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Update your account's name, email address, and phone number.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-input label="First name" name="first_name" :value="old('first_name', $user->first_name)" required autofocus />
            <x-input label="Last name" name="last_name" :value="old('last_name', $user->last_name)" required />
        </div>

        <x-input label="Email address" name="email" type="email" :value="old('email', $user->email)" required />
        <x-input label="Phone number" name="phone" :value="old('phone', $user->phone)" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <x-alert variant="warning">
                Your email address is unverified.
                <button form="send-verification" class="font-semibold underline">Click here to re-send the verification email.</button>
                @if (session('status') === 'verification-link-sent')
                    <span class="mt-1 block font-medium text-emerald-700 dark:text-emerald-300">A new verification link has been sent to your email address.</span>
                @endif
            </x-alert>
        @endif

        <div class="flex items-center gap-4">
            <x-button type="submit">Save</x-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-emerald-600">Saved.</p>
            @endif
        </div>
    </form>
</section>
