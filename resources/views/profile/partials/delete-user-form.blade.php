<section class="space-y-4">
    <header>
        <h2 class="text-base font-semibold text-red-700 dark:text-red-400">Delete Account</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
            your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn-danger"
    >Delete Account</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Are you sure you want to delete your account?</h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please
                enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" class="form-input" placeholder="Password">
                @error('password', 'userDeletion')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">Cancel</button>
                <button type="submit" class="btn-danger">Delete Account</button>
            </div>
        </form>
    </x-modal>
</section>
