<section>
    <header>
        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Update Password</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ensure your account is using a long, random password to stay secure.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="form-label">Current Password</label>
            <input id="current_password" name="current_password" type="password" class="form-input" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">New Password</label>
            <input id="password" name="password" type="password" class="form-input" autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <x-button type="submit">Save</x-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-emerald-600">Saved.</p>
            @endif
        </div>
    </form>
</section>
