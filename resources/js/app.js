import './bootstrap';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';

Alpine.plugin(intersect);

Alpine.data('heroSlider', (count, intervalMs) => ({
    active: 0,
    count,
    intervalMs,
    progress: 0,
    timer: null,
    progressTimer: null,
    touchStartX: null,

    start() {
        this.tick();
    },

    tick() {
        this.clearTimers();
        this.progress = 0;

        const stepMs = 50;
        this.progressTimer = setInterval(() => {
            this.progress = Math.min(100, this.progress + (stepMs / this.intervalMs) * 100);
        }, stepMs);
        this.timer = setTimeout(() => this.next(), this.intervalMs);
    },

    clearTimers() {
        clearTimeout(this.timer);
        clearInterval(this.progressTimer);
    },

    pause() {
        this.clearTimers();
    },

    resume() {
        this.tick();
    },

    next() {
        this.active = (this.active + 1) % this.count;
        this.tick();
    },

    prev() {
        this.active = (this.active - 1 + this.count) % this.count;
        this.tick();
    },

    goTo(index) {
        this.active = index;
        this.tick();
    },

    handleTouchStart(e) {
        this.touchStartX = e.changedTouches[0].clientX;
    },

    handleTouchEnd(e) {
        if (this.touchStartX === null) return;

        const deltaX = e.changedTouches[0].clientX - this.touchStartX;
        if (Math.abs(deltaX) > 50) {
            deltaX > 0 ? this.prev() : this.next();
        }
        this.touchStartX = null;
    },
}));

Alpine.data('typewriterSlide', (title, subtitle) => ({
    typedTitle: '',
    typedSubtitle: '',
    typingTitle: false,
    typingSubtitle: false,
    timers: [],

    type() {
        this.reset();
        this.typingTitle = true;

        let i = 0;
        const typeTitleChar = () => {
            this.typedTitle = title.slice(0, i);
            i++;
            if (i <= title.length) {
                this.timers.push(setTimeout(typeTitleChar, 45));
            } else {
                this.typingTitle = false;
                this.typeSubtitle();
            }
        };
        typeTitleChar();
    },

    typeSubtitle() {
        if (!subtitle) return;
        this.typingSubtitle = true;

        let j = 0;
        const typeSubtitleChar = () => {
            this.typedSubtitle = subtitle.slice(0, j);
            j++;
            if (j <= subtitle.length) {
                this.timers.push(setTimeout(typeSubtitleChar, 20));
            } else {
                this.typingSubtitle = false;
            }
        };
        typeSubtitleChar();
    },

    reset() {
        this.timers.forEach((t) => clearTimeout(t));
        this.timers = [];
        this.typedTitle = '';
        this.typedSubtitle = '';
        this.typingTitle = false;
        this.typingSubtitle = false;
    },
}));

Alpine.data('networkBackground', () => ({
    raf: null,
    resizeHandler: null,

    start() {
        if (this.raf) return;

        const canvas = this.$refs.networkCanvas;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = 0;
        let height = 0;
        let particles = [];

        const spawn = () => {
            const count = Math.max(28, Math.round((width * height) / 9000));
            particles = Array.from({ length: count }, () => ({
                x: Math.random() * width,
                y: Math.random() * height,
                vx: (Math.random() - 0.5) * 0.25,
                vy: (Math.random() - 0.5) * 0.25,
            }));
        };

        const resize = () => {
            width = canvas.width = canvas.offsetWidth;
            height = canvas.height = canvas.offsetHeight;
            spawn();
        };
        this.resizeHandler = resize;
        window.addEventListener('resize', resize);
        requestAnimationFrame(resize);

        const linkDist = 110;

        const draw = () => {
            ctx.clearRect(0, 0, width, height);

            particles.forEach((p) => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;
            });

            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const a = particles[i];
                    const b = particles[j];
                    const dist = Math.hypot(a.x - b.x, a.y - b.y);
                    if (dist < linkDist) {
                        ctx.strokeStyle = `rgba(230, 173, 48, ${0.55 * (1 - dist / linkDist)})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.stroke();
                    }
                }
            }

            particles.forEach((p) => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 2.2, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
                ctx.fill();
            });

            this.raf = requestAnimationFrame(draw);
        };

        this.raf = requestAnimationFrame(draw);
    },

    stop() {
        if (this.raf) {
            cancelAnimationFrame(this.raf);
            this.raf = null;
        }
        if (this.resizeHandler) {
            window.removeEventListener('resize', this.resizeHandler);
            this.resizeHandler = null;
        }
    },
}));

Alpine.data('commandPalette', () => ({
    open: false,
    query: '',
    loading: false,
    groups: [],
    activeIndex: -1,
    debounceTimer: null,

    init() {
        window.addEventListener('open-command-palette', () => this.openPalette());
        window.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                this.openPalette();
            }
        });
    },

    openPalette() {
        this.open = true;
        this.query = '';
        this.groups = [];
        this.activeIndex = -1;
        this.$nextTick(() => this.$refs.paletteInput?.focus());
    },

    close() {
        this.open = false;
    },

    onInput() {
        clearTimeout(this.debounceTimer);
        this.activeIndex = -1;

        const term = this.query.trim();
        if (term.length < 2) {
            this.groups = [];
            this.loading = false;
            return;
        }

        this.loading = true;
        this.debounceTimer = setTimeout(() => this.runSearch(term), 300);
    },

    runSearch(term) {
        fetch(`/search?q=${encodeURIComponent(term)}`, { headers: { Accept: 'application/json' } })
            .then((r) => r.json())
            .then((data) => {
                this.groups = data.groups || [];
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
    },

    flatItems() {
        return this.groups.flatMap((g) => g.items);
    },

    isActive(url) {
        const items = this.flatItems();
        return items[this.activeIndex]?.url === url;
    },

    moveDown() {
        const items = this.flatItems();
        if (!items.length) return;
        this.activeIndex = Math.min(this.activeIndex + 1, items.length - 1);
    },

    moveUp() {
        const items = this.flatItems();
        if (!items.length) return;
        this.activeIndex = Math.max(this.activeIndex - 1, 0);
    },

    selectActive() {
        const item = this.flatItems()[this.activeIndex];
        if (item) window.location.href = item.url;
    },
}));

window.Alpine = Alpine;
window.Swal = Swal;
window.flatpickr = flatpickr;

window.AlumniNetwork = {
    csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    },

    sendConnectionRequest(userId, btn) {
        if (btn) btn.disabled = true;

        fetch(`/network/${userId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': this.csrfToken(), Accept: 'application/json' },
        })
            .then((r) => r.json().then((data) => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                Swal.fire({
                    icon: ok ? 'success' : 'error',
                    title: data.message,
                    timer: ok ? 1500 : undefined,
                    showConfirmButton: !ok,
                });
                if (ok && btn) {
                    btn.textContent = 'Requested';
                } else if (btn) {
                    btn.disabled = false;
                }
            })
            .catch(() => {
                if (btn) btn.disabled = false;
                Swal.fire({ icon: 'error', title: 'Something went wrong.' });
            });
    },
};

window.AlumniCommunity = {
    toggleLike(type, id, btn) {
        fetch(`/likes/${type}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': AlumniNetwork.csrfToken(), Accept: 'application/json' },
        })
            .then((r) => r.json())
            .then((data) => {
                btn.dataset.liked = data.liked ? '1' : '0';
                btn.classList.toggle('text-red-600', data.liked);
                btn.classList.toggle('text-slate-500', !data.liked);
                btn.querySelector('.like-count').textContent = data.count;
            });
    },
};

window.reportContent = function (type, id) {
    Swal.fire({
        title: 'Report this content',
        input: 'select',
        inputOptions: {
            spam: 'Spam',
            harassment: 'Harassment',
            inappropriate: 'Inappropriate content',
            misinformation: 'Misinformation',
            other: 'Other',
        },
        inputPlaceholder: 'Select a reason',
        showCancelButton: true,
        confirmButtonText: 'Submit Report',
    }).then((result) => {
        if (!result.isConfirmed || !result.value) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/reports/${type}/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${AlumniNetwork.csrfToken()}">
            <input type="hidden" name="reason" value="${result.value}">
        `;
        document.body.appendChild(form);
        form.submit();
    });
};

Alpine.start();
