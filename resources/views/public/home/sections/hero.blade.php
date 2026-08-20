<x-hero-slider :slides="$sliders">
    <section class="relative overflow-hidden rounded-3xl bg-navy-950 text-white shadow-xl">
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 28px 28px;"></div>

        <div class="section-container relative py-14 sm:py-20 lg:py-24">
            <div class="mx-auto max-w-3xl text-center">
                <h1 class="text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">
                    Connect. Engage. Inspire. <span class="text-gold-400">Give Back.</span>
                </h1>

                <p class="mx-auto mt-5 max-w-xl text-lg text-navy-200">
                    Reconnect with your university community and build meaningful professional relationships
                    with alumni around the world.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <x-button :href="route('register')" variant="gold" class="w-full sm:w-auto">
                        Join the Alumni Network
                    </x-button>
                    <x-button :href="route('alumni.directory')" variant="secondary" class="w-full bg-white/10 text-white ring-white/20 hover:bg-white/20 sm:w-auto">
                        Explore Alumni Directory
                    </x-button>
                </div>
            </div>
        </div>
    </section>
</x-hero-slider>
