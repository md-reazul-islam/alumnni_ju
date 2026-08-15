@props(['slides'])

@if ($slides->isEmpty())
    {{ $slot }}
@else
    <section
        x-data="heroSlider({{ $slides->count() }}, 4000)"
        x-init="start()"
        @mouseenter="pause()"
        @mouseleave="resume()"
        @keydown.left="prev()"
        @keydown.right="next()"
        @touchstart="handleTouchStart($event)"
        @touchend="handleTouchEnd($event)"
        tabindex="0"
        role="region"
        aria-roledescription="carousel"
        aria-label="Featured highlights"
        class="group relative h-[80vh] min-h-[480px] max-h-[720px] overflow-hidden bg-navy-950 text-white outline-none"
    >
        <div class="absolute inset-0 h-full w-full">
            @foreach ($slides as $slide)
                <div
                    class="absolute inset-0 h-full w-full transition-all ease-in-out"
                    style="transition-duration: 1200ms;"
                    :class="active === {{ $loop->index }} ? 'opacity-100 blur-0 z-10' : 'opacity-0 blur-lg z-0'"
                    :aria-hidden="active === {{ $loop->index }} ? 'false' : 'true'"
                >
                    <img
                        src="{{ $slide->image_url }}"
                        alt="{{ $slide->title }}"
                        class="absolute inset-0 h-full w-full object-cover transition-transform ease-out"
                        style="transition-duration: 5200ms;"
                        :class="active === {{ $loop->index }} ? 'scale-110' : 'scale-100'"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/60 to-navy-950/10"></div>

                    <div class="section-container relative flex h-full items-end pb-20 sm:items-center sm:pb-0">
                        <div
                            class="max-w-2xl transition-all duration-700 ease-out"
                            :class="active === {{ $loop->index }} ? 'opacity-100 translate-y-0 delay-300' : 'opacity-0 translate-y-4'"
                        >
                            @if ($slide->subtitle)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-gold-300 ring-1 ring-inset ring-white/20">
                                    <x-icon name="sparkles" class="h-3.5 w-3.5" /> {{ config('app.name') }}
                                </span>
                            @endif

                            <h1 class="mt-5 text-3xl font-bold leading-tight sm:text-5xl lg:text-6xl">
                                {{ $slide->title }}
                            </h1>

                            @if ($slide->subtitle)
                                <p class="mt-4 max-w-xl text-base text-navy-100 sm:text-lg">
                                    {{ $slide->subtitle }}
                                </p>
                            @endif

                            @if ($slide->button_text && $slide->button_url)
                                <div class="mt-7">
                                    <x-button :href="$slide->button_url" variant="gold">
                                        {{ $slide->button_text }}
                                    </x-button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <button
                type="button"
                @click="prev()"
                aria-label="Previous slide"
                class="absolute left-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white opacity-0 backdrop-blur transition hover:bg-white/20 focus:opacity-100 group-hover:opacity-100 sm:left-6"
            >
                <x-icon name="chevron-left" class="h-5 w-5" />
            </button>
            <button
                type="button"
                @click="next()"
                aria-label="Next slide"
                class="absolute right-3 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white opacity-0 backdrop-blur transition hover:bg-white/20 focus:opacity-100 group-hover:opacity-100 sm:right-6"
            >
                <x-icon name="chevron-right" class="h-5 w-5" />
            </button>

            <div class="absolute inset-x-0 bottom-6 z-10 flex items-center justify-center gap-2">
                <template x-for="(slide, index) in Array.from({ length: {{ $slides->count() }} })" :key="index">
                    <button
                        type="button"
                        @click="goTo(index)"
                        :aria-label="`Go to slide ${index + 1}`"
                        :aria-current="active === index"
                        class="relative h-1.5 overflow-hidden rounded-full bg-white/30 transition-all"
                        :class="active === index ? 'w-8' : 'w-1.5'"
                    >
                        <span
                            x-show="active === index"
                            x-cloak
                            class="absolute inset-y-0 left-0 rounded-full bg-gold-400"
                            :style="`width: ${progress}%`"
                        ></span>
                    </button>
                </template>
            </div>
        @endif
    </section>
@endif
