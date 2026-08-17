<x-layouts::app>
  <div class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
    <div class="section-container max-w-2xl py-12">
        <x-breadcrumb :items="[['label' => 'Donate', 'url' => route('donations.index')], ['label' => 'Checkout']]" class="mb-6" />

        <div class="card card-body">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                {{ $campaign ? 'Donate to ' . $campaign->title : 'Make a Donation' }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Your contribution supports our alumni community.</p>

            @if ($errors->any())
                <x-alert variant="danger" class="mt-4">{{ $errors->first() }}</x-alert>
            @endif

            <form
                method="POST"
                action="{{ route('donations.store', $campaign) }}"
                x-data="{ amount: 100, anonymous: false }"
                class="mt-6 space-y-5"
            >
                @csrf

                <div>
                    <label class="form-label">Amount (USD)</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ([25, 50, 100, 250] as $preset)
                            <button type="button" @click="amount = {{ $preset }}" :class="amount === {{ $preset }} ? 'border-navy-700 bg-navy-50 text-navy-800 dark:bg-navy-800 dark:text-white' : 'border-slate-200 dark:border-navy-700'" class="rounded-lg border py-2 text-sm font-medium">
                                ${{ $preset }}
                            </button>
                        @endforeach
                    </div>
                    <input type="number" name="amount" x-model="amount" min="1" step="1" class="form-input mt-3" required>
                </div>

                <x-select label="Purpose" name="category" required :options="[
                    'scholarship' => 'Scholarship', 'research' => 'Research', 'student_support' => 'Student Support',
                    'infrastructure' => 'Infrastructure', 'emergency_fund' => 'Emergency Fund',
                    'alumni_association' => 'Alumni Association', 'general_fund' => 'General Fund',
                ]" :selected="$campaign?->category" />

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_anonymous" value="1" x-model="anonymous" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500">
                    Donate anonymously
                </label>

                <div x-show="!anonymous">
                    <x-input label="Full Name" name="donor_name" :value="auth()->user()?->full_name" />
                </div>

                <x-input label="Email Address" name="donor_email" type="email" :value="auth()->user()?->email" required />

                <x-select label="Payment Method" name="payment_method" required :options="['card' => 'Credit / Debit Card', 'bank_transfer' => 'Bank Transfer', 'paypal' => 'PayPal', 'other' => 'Other']" />

                <p class="text-xs text-slate-400">
                    Payments are processed securely. We never store your card details on our servers.
                </p>

                <x-button type="submit" variant="gold" class="w-full">Complete Donation</x-button>
            </form>
        </div>
    </div>
  </div>
</x-layouts::app>
