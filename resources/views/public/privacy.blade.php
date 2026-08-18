<x-layouts::app>
    <section class="overflow-hidden rounded-3xl bg-white shadow-xl dark:bg-navy-900">
      <div class="section-container max-w-3xl py-10 sm:py-14">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Privacy Policy</h1>
        <p class="mt-2 text-sm text-slate-400">Last updated {{ now()->format('F Y') }}</p>

        <div class="prose prose-slate mt-8 max-w-none dark:prose-invert prose-headings:font-semibold">
            <h2>Information We Collect</h2>
            <p>
                When you register as an alumni member, we collect personal information including your name, contact
                details, academic history (department, program, degree, graduation year), and professional details
                you choose to share such as employment history and skills.
            </p>

            <h2>How We Use Your Information</h2>
            <p>
                We use your information to verify your alumni status, operate the directory, facilitate networking
                and mentorship connections, send event and career notifications, and process donations. We never
                sell your personal data to third parties.
            </p>

            <h2>Profile Visibility</h2>
            <p>
                You control who can see your profile through three visibility levels: Public (visible to anyone),
                Alumni Only (visible to verified members), and Private (visible only to accepted connections).
                Contact details such as your email and phone number are never exposed through public pages or APIs
                regardless of your visibility setting.
            </p>

            <h2>Data Security</h2>
            <p>
                Passwords are hashed using industry-standard algorithms and never stored in plain text. We apply
                CSRF protection, rate limiting, and role-based access control throughout the platform, and all
                administrative actions are recorded in an audit log.
            </p>

            <h2>Your Rights</h2>
            <p>
                You may update or delete your profile information at any time from your account settings, or
                contact the alumni office to request a full export or deletion of your data.
            </p>

            <h2>Contact Us</h2>
            <p>
                Questions about this policy can be directed to our <a href="{{ route('contact') }}">contact page</a>.
            </p>
        </div>
      </div>
    </section>
</x-layouts::app>
