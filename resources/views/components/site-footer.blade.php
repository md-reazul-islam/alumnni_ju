<footer class="border-t border-slate-200 bg-white dark:border-navy-800 dark:bg-navy-950">
    <div class="section-container grid grid-cols-2 gap-8 py-12 sm:grid-cols-2 lg:grid-cols-5">
        <div class="col-span-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-base font-bold text-navy-900 dark:text-white">
                <x-brand-icon />
                {{ \App\Models\Setting::get('general', 'site_text', config('app.name')) }}
            </a>
            <p class="mt-3 max-w-sm text-sm text-slate-500 dark:text-slate-400">
                Connecting graduates worldwide through networking, mentorship, career opportunities, and lifelong community.
            </p>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Explore</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('about') }}" class="hover:text-navy-800 dark:hover:text-white">About</a></li>
                @if (Route::has('alumni.directory'))
                    <li><a href="{{ route('alumni.directory') }}" class="hover:text-navy-800 dark:hover:text-white">Alumni Directory</a></li>
                @endif
                @if (Route::has('events.index'))
                    <li><a href="{{ route('events.index') }}" class="hover:text-navy-800 dark:hover:text-white">Events</a></li>
                @endif
                @if (Route::has('news.index'))
                    <li><a href="{{ route('news.index') }}" class="hover:text-navy-800 dark:hover:text-white">News</a></li>
                @endif
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Get Involved</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                @if (Route::has('jobs.index'))
                    <li><a href="{{ route('jobs.index') }}" class="hover:text-navy-800 dark:hover:text-white">Career Center</a></li>
                @endif
                @if (Route::has('scholarships.index'))
                    <li><a href="{{ route('scholarships.index') }}" class="hover:text-navy-800 dark:hover:text-white">Scholarships</a></li>
                @endif
                @if (Route::has('donations.index'))
                    <li><a href="{{ route('donations.index') }}" class="hover:text-navy-800 dark:hover:text-white">Donate</a></li>
                @endif
                <li><a href="{{ route('register') }}" class="hover:text-navy-800 dark:hover:text-white">Become a Member</a></li>
            </ul>
        </div>

        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Legal</p>
            <ul class="mt-3 space-y-2 text-sm text-slate-500 dark:text-slate-400">
                <li><a href="{{ route('privacy') }}" class="hover:text-navy-800 dark:hover:text-white">Privacy Policy</a></li>
                <li><a href="{{ route('terms') }}" class="hover:text-navy-800 dark:hover:text-white">Terms &amp; Conditions</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-navy-800 dark:hover:text-white">Contact Us</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-100 py-6 dark:border-navy-800">
        <p class="section-container text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</footer>
