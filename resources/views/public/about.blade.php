<x-layouts::app>
    <section class="bg-navy-950 py-16 text-center text-white sm:py-20">
        <div class="section-container">
            <h1 class="text-3xl font-bold sm:text-4xl">About {{ config('app.name') }}</h1>
            <p class="mx-auto mt-3 max-w-2xl text-navy-200">
                A lifelong community connecting graduates, faculty, and friends of the university across the world.
            </p>
        </div>
    </section>

    <section class="section-container grid grid-cols-1 gap-10 py-16 sm:py-20 lg:grid-cols-2">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Our Mission</h2>
            <p class="mt-3 text-slate-600 dark:text-slate-300">
                The Alumni Association exists to strengthen the bond between the university and its graduates —
                fostering professional networking, mentorship, philanthropy, and community engagement that spans
                generations. We believe an alumni network is a lifelong resource, not a one-time membership.
            </p>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">What We Do</h2>
            <ul class="mt-3 space-y-3 text-slate-600 dark:text-slate-300">
                <li class="flex gap-3"><x-icon name="users" class="mt-0.5 h-5 w-5 flex-shrink-0 text-navy-600" /> Connect alumni with each other through a searchable global directory.</li>
                <li class="flex gap-3"><x-icon name="briefcase" class="mt-0.5 h-5 w-5 flex-shrink-0 text-navy-600" /> Power a career center for job postings, mentorship, and referrals.</li>
                <li class="flex gap-3"><x-icon name="calendar" class="mt-0.5 h-5 w-5 flex-shrink-0 text-navy-600" /> Host reunions, workshops, and regional meetups worldwide.</li>
                <li class="flex gap-3"><x-icon name="heart" class="mt-0.5 h-5 w-5 flex-shrink-0 text-navy-600" /> Support scholarships and student programs through alumni giving.</li>
            </ul>
        </div>
    </section>

    <section class="border-t border-slate-100 bg-slate-50 py-16 dark:border-navy-800 dark:bg-navy-900 sm:py-20">
        <div class="section-container text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Ready to reconnect?</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Join thousands of alumni already part of the network.</p>
            <x-button :href="route('register')" class="mt-6">Join the Alumni Network</x-button>
        </div>
    </section>
</x-layouts::app>
