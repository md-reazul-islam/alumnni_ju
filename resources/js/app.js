import './bootstrap';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';

Alpine.plugin(intersect);

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
