document.addEventListener('DOMContentLoaded', () => {
    // Rating bintang interaktif (Public Pulse)
    const stars = document.querySelectorAll('.rating-star');
    const paint = (value) => {
        stars.forEach((star) => {
            star.classList.toggle('is-active', Number(star.dataset.value) <= value);
        });
    };
    let selected = 4;
    paint(selected);

    stars.forEach((star) => {
        star.addEventListener('mouseenter', () => paint(Number(star.dataset.value)));
        star.addEventListener('focus', () => paint(Number(star.dataset.value)));
        star.addEventListener('click', () => {
            selected = Number(star.dataset.value);
            paint(selected);
        });
    });
    const box = document.querySelector('.rating-box__stars');
    if (box) {
        box.addEventListener('mouseleave', () => paint(selected));
    }

    // Tampilkan / sembunyikan password
    document.querySelectorAll('.password-toggle').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.password-field').querySelector('input');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    // Navbar: tandai link aktif sesuai section yang terlihat
    const links = document.querySelectorAll('.cz-navbar .nav-link');
    const sections = [...links]
        .map((a) => a.getAttribute('href'))
        .filter((h) => h && h.startsWith('#'))
        .map((h) => document.querySelector(h))
        .filter(Boolean);

    if (sections.length && 'IntersectionObserver' in window) {
        const setActive = (hash) => {
            links.forEach((a) => 
                a.classList.toggle('active', a.getAttribute('href') === hash));
        };
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) setActive('#' + entry.target.id);
                });
            },
            { rootMargin: '-40% 0px -55% 0px' }
        );
        sections.forEach((s) => observer.observe(s));

        window.addEventListener('scroll', () => {
            if (window.scrollY < 120) {
                links.forEach((a) => a.classList.toggle('active', a.getAttribute('href') === '/'));
            }
        }, { passive: true });
    }
});

// ---------------------------------------------------------------------------
// Dashboard: sidebar offcanvas (mobile) + grafik Chart.js. Hanya aktif jika elemennya ada.
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar offcanvas
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    const setOpen = (open) => {
        document.body.classList.toggle('sidebar-open', open);
        document.querySelectorAll('.cz-topbar__menu').forEach((b) => b.setAttribute('aria-expanded', String(open)));
    };
    toggles.forEach((el) => el.addEventListener('click', () => setOpen(!document.body.classList.contains('sidebar-open'))));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
    window.matchMedia('(min-width: 992px)').addEventListener('change', (m) => { if (m.matches) setOpen(false); });
 
    if (!window.Chart) return;
    const css = getComputedStyle(document.documentElement);
    const text = css.getPropertyValue('--cityzen-text').trim() || '#081F44';
    const muted = css.getPropertyValue('--cityzen-text-secondary').trim() || '#434750';
    const light = css.getPropertyValue('--cityzen-primary-light').trim() || '#B7C3E8';
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
 
    // Grafik batang: penuntasan tepat waktu
    const bar = document.getElementById('chartOnTime');
    if (bar) {
        const labels = JSON.parse(bar.dataset.labels);
        const values = JSON.parse(bar.dataset.values);
        const colors = values.map((_, i) => (i === values.length - 1 ? '#3558A8' : light));
 
        const valueLabels = {
            id: 'valueLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = "700 12px 'Plus Jakarta Sans', 'Inter', sans-serif";
                ctx.fillStyle = text;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                chart.getDatasetMeta(0).data.forEach((b, i) => ctx.fillText(values[i] + '%', b.x, b.y - 6));
                ctx.restore();
            },
        };
 
        new Chart(bar, {
            type: 'bar',
            data: { labels, datasets: [{ data: values, backgroundColor: colors, borderRadius: { topLeft: 6, topRight: 6 }, borderSkipped: 'bottom', barPercentage: 0.5, categoryPercentage: 1 }] },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 22 } },
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => ` ${c.parsed.y}% selesai tepat waktu` } } },
                scales: {
                    y: { min: 0, max: 100, display: false },
                    x: { grid: { display: false }, border: { color: '#E2E7F3' }, ticks: { color: muted, font: { size: 12 } } },
                },
            },
            plugins: [valueLabels],
        });
    }
 
    // Grafik donat: status laporan
    const donut = document.getElementById('chartStatus');
    if (donut) {
        new Chart(donut, {
            type: 'doughnut',
            data: {
                labels: JSON.parse(donut.dataset.labels),
                datasets: [{ data: JSON.parse(donut.dataset.values), backgroundColor: JSON.parse(donut.dataset.colors), borderWidth: 0, hoverOffset: 4 }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                plugins: { legend: { display: false } },
            },
        });
    }
});

// ---------------------------------------------------------------------------
// Jelajahi: pencarian, filter, urutan, pilih laporan, peta ilustrasi, modal detail.
// Hanya aktif di halaman yang memiliki #jelajahi. Data dibaca dari
// <script id="jelajahiData"> (dummy data di Blade; ganti dengan data backend).
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('jelajahi');
    if (!root) return;

    const reports = JSON.parse(document.getElementById('jelajahiData').textContent);
    const byId = new Map(reports.map((r) => [String(r.id), r]));
    const haystack = new Map(reports.map((r) => [
        String(r.id),
        [r.title, r.ticket, r.area, r.city, r.category_label].join(' ').toLowerCase(),
    ]));
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const list = root.querySelector('[data-jl-list]');
    const cards = [...list.querySelectorAll('.jl-card')];
    const cardById = new Map(cards.map((c) => [c.dataset.id, c]));
    const markers = [...root.querySelectorAll('.jl-marker')];
    const chips = [...root.querySelectorAll('[data-jl-chip]')];
    const emptyEl = root.querySelector('[data-jl-empty]');
    const countEl = root.querySelector('[data-jl-count]');
    const info = root.querySelector('[data-jl-info]');
    const world = root.querySelector('[data-jl-world]');
    const zoomBtns = [...root.querySelectorAll('[data-jl-zoom]')];
    const modalEl = document.getElementById('jelajahiModal');
    const supportBtn = modalEl.querySelector('[data-jl-support]');

    const ZOOM_MIN = 1, ZOOM_MAX = 1.5, ZOOM_STEP = 0.25;
    const state = { category: 'semua', status: 'semua', sort: 'terbaru', q: '', selected: null, zoom: 1, modalId: null };
    const supported = new Set(); // laporan yang didukung pengguna (hanya di memori, tidak disimpan)

    const $ = (sel, scope = root) => scope.querySelector(sel);

    // ----- Pencarian: memakai kolom cari di topbar agar tidak ada search bar ganda -----
    const topInput = document.querySelector('.cz-search__input');
    if (topInput) {
        const fromUrl = new URLSearchParams(window.location.search).get('q');
        if (fromUrl) { topInput.value = fromUrl; state.q = fromUrl.trim().toLowerCase(); }
        topInput.addEventListener('input', () => { state.q = topInput.value.trim().toLowerCase(); render(); });
        topInput.closest('form')?.addEventListener('submit', (e) => e.preventDefault());
    }

    // ----- Render: terapkan filter + urutan ke daftar, marker, dan jumlah -----
    const matches = (r) =>
        (state.category === 'semua' || r.category === state.category) &&
        (state.status === 'semua' || r.status === state.status) &&
        (!state.q || haystack.get(String(r.id)).includes(state.q));

    function render() {
        const visible = reports.filter(matches);
        const visibleIds = new Set(visible.map((r) => String(r.id)));

        cards.forEach((c) => { c.hidden = !visibleIds.has(c.dataset.id); });
        markers.forEach((m) => { m.hidden = !visibleIds.has(m.dataset.id); });

        const byRecent = (a, b) => a.minutes - b.minutes;
        const byVotes = (a, b) => b.votes - a.votes;
        [...visible].sort(state.sort === 'populer' ? byVotes : byRecent)
            .forEach((r) => list.appendChild(cardById.get(String(r.id))));

        list.hidden = visible.length === 0;
        emptyEl.hidden = visible.length > 0;
        countEl.textContent = `Menampilkan ${visible.length} dari ${reports.length} laporan`;

        if (!state.selected || !visibleIds.has(state.selected)) {
            const first = list.querySelector('.jl-card:not([hidden])');
            select(first ? first.dataset.id : null);
        }
    }

    // ----- Pilih laporan: sorot kartu + marker, isi ringkasan di peta -----
    function select(id, { scroll = false } = {}) {
        state.selected = id ? String(id) : null;
        cards.forEach((c) => {
            const on = c.dataset.id === state.selected;
            c.classList.toggle('is-active', on);
            $('.jl-card__title-btn', c).setAttribute('aria-pressed', String(on));
        });
        markers.forEach((m) => m.classList.toggle('is-active', m.dataset.id === state.selected));
        fillInfo();
        if (scroll && state.selected) scrollCardIntoView(cardById.get(state.selected));
    }

    function scrollCardIntoView(card) {
        if (list.scrollHeight <= list.clientHeight) return; // di mobile daftar tidak punya scroll sendiri
        const top = card.offsetTop, bottom = top + card.offsetHeight;
        if (top < list.scrollTop || bottom > list.scrollTop + list.clientHeight) {
            list.scrollTo({ top: top - 8, behavior: reduceMotion ? 'auto' : 'smooth' });
        }
    }

    const cardParts = (id) => {
        const card = cardById.get(String(id));
        return { icon: $('.jl-card__icon', card).innerHTML, badge: $('.dash-badge', card).outerHTML };
    };

    function fillInfo() {
        const r = byId.get(state.selected);
        info.hidden = !r;
        if (!r) return;
        const parts = cardParts(r.id);
        $('[data-jl-info-icon]').innerHTML = parts.icon;
        $('[data-jl-info-badge]').innerHTML = parts.badge;
        $('[data-jl-info-time]').textContent = r.time;
        $('[data-jl-info-title]').textContent = r.title;
        $('[data-jl-info-place]').textContent = `${r.area}, ${r.city}`;
        $('[data-jl-info-votes]').textContent = `${r.votes} dukungan`;
    }

    // ----- Modal detail -----
    function openDetail(id) {
        const r = byId.get(String(id));
        if (!r || !window.bootstrap) return;
        state.modalId = String(r.id);
        const parts = cardParts(r.id);
        $('[data-jl-modal-icon]', modalEl).innerHTML = parts.icon;
        $('[data-jl-modal-badge]', modalEl).innerHTML = parts.badge;
        $('[data-jl-modal-category]', modalEl).textContent = r.category_label;
        $('[data-jl-modal-title]', modalEl).textContent = r.title;
        $('[data-jl-modal-ticket]', modalEl).textContent = r.ticket;
        $('[data-jl-modal-place]', modalEl).textContent = `${r.area}, ${r.city}`;
        $('[data-jl-modal-time]', modalEl).textContent = r.time;
        $('[data-jl-modal-desc]', modalEl).textContent = r.description;
        paintSupport(r);
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function paintSupport(r) {
        const on = supported.has(String(r.id));
        supportBtn.setAttribute('aria-pressed', String(on));
        $('[data-jl-support-label]', modalEl).textContent = on ? 'Didukung' : 'Dukung laporan';
        $('[data-jl-modal-votes]', modalEl).textContent = `${r.votes} orang`;
    }

    supportBtn.addEventListener('click', () => {
        const r = byId.get(state.modalId);
        if (!r) return;
        const id = String(r.id);
        if (supported.has(id)) { supported.delete(id); r.votes -= 1; } else { supported.add(id); r.votes += 1; }
        $('[data-jl-votes]', cardById.get(id)).textContent = r.votes;
        if (state.selected === id) fillInfo();
        paintSupport(r);
    });
    modalEl.addEventListener('hidden.bs.modal', () => { if (state.sort === 'populer') render(); });

    // ----- Zoom peta ilustrasi (memperbesar tampilan saja; belum peta sungguhan) -----
    function setZoom(z) {
        state.zoom = Math.min(ZOOM_MAX, Math.max(ZOOM_MIN, z));
        const r = byId.get(state.selected);
        world.style.transformOrigin = r && r.x != null ? `${r.x}% ${r.y}%` : '50% 50%'; // marker terpilih tetap terlihat
        world.style.setProperty('--jl-z', state.zoom);
        zoomBtns.forEach((b) => {
            b.disabled = (b.dataset.jlZoom === 'in' && state.zoom >= ZOOM_MAX) || (b.dataset.jlZoom === 'out' && state.zoom <= ZOOM_MIN);
        });
    }
    zoomBtns.forEach((b) => b.addEventListener('click', () => setZoom(state.zoom + (b.dataset.jlZoom === 'in' ? ZOOM_STEP : -ZOOM_STEP))));

    // ----- Filter: chip kategori, dropdown status & urutan -----
    function setChip(value) {
        state.category = value;
        chips.forEach((c) => {
            const on = c.dataset.jlChip === value;
            c.classList.toggle('is-active', on);
            c.setAttribute('aria-pressed', String(on));
        });
    }
    chips.forEach((c) => c.addEventListener('click', () => { setChip(c.dataset.jlChip); render(); }));

    function setOption(type, value) {
        state[type] = value;
        root.querySelectorAll(`[data-jl-option="${type}"]`).forEach((o) => {
            const on = o.dataset.value === value;
            o.classList.toggle('is-active', on);
            o.setAttribute('aria-checked', String(on));
            if (on) $(`[data-jl-toggle-label="${type}"]`).textContent = o.dataset.label;
        });
    }
    root.querySelectorAll('[data-jl-option]').forEach((o) =>
        o.addEventListener('click', () => { setOption(o.dataset.jlOption, o.dataset.value); render(); }));

    // ----- Klik: kartu, marker, tombol detail, atur ulang -----
    list.addEventListener('click', (e) => {
        const card = e.target.closest('.jl-card');
        if (!card) return;
        if (e.target.closest('[data-jl-open]')) openDetail(card.dataset.id);
        else if (e.target.closest('.jl-card__title-btn')) select(card.dataset.id);
    });
    markers.forEach((m) => m.addEventListener('click', () => select(m.dataset.id, { scroll: true })));
    info.querySelector('[data-jl-open]').addEventListener('click', () => openDetail(state.selected));

    root.querySelector('[data-jl-reset]').addEventListener('click', () => {
        setChip('semua');
        setOption('status', 'semua');
        setOption('sort', 'terbaru');
        state.q = '';
        if (topInput) topInput.value = '';
        render();
    });

    setZoom(1);
    render();
});


// ---------------------------------------------------------------------------
// Aduin: form laporan 5 tahap (Kategori > Foto > Lokasi > Detail > Tinjau).
// Semua tahap berada dalam satu <form>; tahap lain hanya disembunyikan (hidden),
// jadi data tidak hilang saat pindah tahap. Hanya aktif jika ada #aduin.
// Pengiriman masih simulasi: ganti handler 'submit' di bawah dengan POST ke backend.
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('aduin');
    if (!root) return;

    const form = document.getElementById('aduinForm');
    const $ = (sel, scope = root) => scope.querySelector(sel);
    const $$ = (sel, scope = root) => [...scope.querySelectorAll(sel)];
    const panels = $$('[data-ad-panel]');
    const stepEls = $$('[data-ad-step]');
    const TOTAL = panels.length;
    const MAX_PHOTOS = 3, MAX_BYTES = 10 * 1024 * 1024, TYPES = ['image/jpeg', 'image/png'];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const els = {
        category: $$('input[name="category"]'),
        photoInput: $('#adPhotos'),
        drop: $('[data-ad-drop]'),
        thumbs: $('[data-ad-thumbs]'),
        address: $('#adAddress'),
        title: $('#adTitle'),
        desc: $('#adDesc'),
        counter: $('[data-ad-counter]'),
        map: $('[data-ad-map]'),
        pin: $('[data-ad-pin]'),
        mapHint: $('[data-ad-map-hint]'),
        mapX: $('[data-ad-mapx]'),
        mapY: $('[data-ad-mapy]'),
        selected: $('[data-ad-selected]'),
        selectedText: $('[data-ad-selected-text]'),
        prev: $('[data-ad-prev]'),
        next: $('[data-ad-next]'),
        submit: $('[data-ad-submit]'),
        reviewBtn: $('[data-ad-review]'),
        sumCount: $('[data-ad-sum-count]'),
        sumHint: $('[data-ad-sum-hint]'),
        success: $('[data-ad-success]'),
    };

    const state = { step: 1, files: [], pin: null };

    // ----- Aturan validasi per tahap: mengembalikan { field: pesan } (kosong = valid) -----
    const checkers = {
        1: () => (els.category.some((r) => r.checked) ? {} : { category: 'Pilih satu kategori untuk melanjutkan.' }),
        2: () => (state.files.length >= 1 ? {} : { photos: 'Unggah minimal 1 foto sebagai bukti.' }),
        3: () => (els.address.value.trim().length >= 5 ? {} : { address: 'Tulis alamat atau patokan lokasi (minimal 5 karakter).' }),
        4: () => {
            const e = {};
            const t = els.title.value.trim().length, d = els.desc.value.trim().length;
            if (t < 8) e.title = 'Judul minimal 8 karakter.';
            if (d < 20) e.description = `Deskripsi minimal 20 karakter (sekarang ${d}).`;
            return e;
        },
        5: () => ({}),
    };
    const isValid = (n) => Object.keys(checkers[n]()).length === 0;

    function setError(field, message) {
        const box = $(`[data-ad-error="${field}"]`);
        if (!box) return;
        box.hidden = !message;
        $('span', box).textContent = message || '';
        box.closest('.ad-field')?.classList.toggle('has-error', Boolean(message));
    }
    function showErrors(n) {
        const errors = checkers[n]();
        $$('[data-ad-panel="' + n + '"] [data-ad-error]').forEach((b) => setError(b.dataset.adError, errors[b.dataset.adError] || ''));
        const first = Object.keys(errors)[0];
        if (first) {
            const target = { category: els.category[0], photos: els.photoInput, address: els.address, title: els.title, description: els.desc }[first];
            target?.focus({ preventScroll: false });
        }
        return Object.keys(errors).length === 0;
    }
    const clearError = (field) => setError(field, '');

    // ----- Navigasi tahap -----
    function goTo(n, { focus = true } = {}) {
        n = Math.min(TOTAL, Math.max(1, n));
        // Maju hanya jika semua tahap sebelumnya valid; jika tidak, berhenti di tahap yang bermasalah.
        for (let i = state.step; i < n; i++) {
            if (!isValid(i)) { if (i !== state.step) setStep(i, focus); showErrors(i); return; }
        }
        setStep(n, focus);
    }

    function setStep(n, focus = true) {
        state.step = n;
        panels.forEach((p) => { p.hidden = Number(p.dataset.adPanel) !== n; });
        stepEls.forEach((li) => {
            const i = Number(li.dataset.adStep);
            li.classList.toggle('is-active', i === n);
            li.classList.toggle('is-done', i < n && isValid(i));
            const btn = $('.ad-step__btn', li);
            i === n ? btn.setAttribute('aria-current', 'step') : btn.removeAttribute('aria-current');
        });
        els.prev.hidden = n === 1;
        els.next.hidden = n === TOTAL;
        els.submit.hidden = n !== TOTAL;
        if (n === TOTAL) fillReview();
        if (n === 3) syncPin();
        if (focus) {
            const heading = $(`[data-ad-panel="${n}"] .ad-panel__title`);
            heading?.focus({ preventScroll: true });
            heading?.scrollIntoView({ block: 'center', behavior: reduceMotion ? 'auto' : 'smooth' });
        }
    }

    root.addEventListener('click', (e) => {
        const go = e.target.closest('[data-ad-goto]');
        if (go) goTo(Number(go.dataset.adGoto));
    });
    els.next.addEventListener('click', () => { if (showErrors(state.step)) goTo(state.step + 1); });
    els.prev.addEventListener('click', () => goTo(state.step - 1));
    els.reviewBtn.addEventListener('click', () => goTo(TOTAL));

    // ----- Tahap 1: kategori -----
    els.category.forEach((r) => r.addEventListener('change', () => { clearError('category'); refresh(); }));

    // ----- Tahap 2: foto (daftar file disimpan di state.files lalu disinkronkan ke <input>) -----
    function addFiles(list) {
        const problems = [];
        [...list].forEach((f) => {
            if (!TYPES.includes(f.type)) problems.push(`"${f.name}" bukan JPG atau PNG.`);
            else if (f.size > MAX_BYTES) problems.push(`"${f.name}" lebih dari 10 MB.`);
            else if (state.files.length >= MAX_PHOTOS) { if (!problems.includes('Maksimal 3 foto.')) problems.push('Maksimal 3 foto.'); }
            else state.files.push({ file: f, url: URL.createObjectURL(f) });
        });
        syncInputFromState();
        renderThumbs();
        setError('photos', problems.join(' '));
        refresh();
    }
    function syncInputFromState() {
        const dt = new DataTransfer();
        state.files.forEach((x) => dt.items.add(x.file));
        els.photoInput.files = dt.files;
    }
    function renderThumbs() {
        els.thumbs.innerHTML = '';
        state.files.forEach((x, i) => {
            const li = document.createElement('li');
            li.className = 'ad-thumb';
            const img = document.createElement('img');
            img.src = x.url; img.alt = `Pratinjau foto ${i + 1}`;
            const rm = document.createElement('button');
            rm.type = 'button'; rm.className = 'ad-thumb__rm'; rm.setAttribute('aria-label', `Hapus foto ${i + 1}`);
            rm.innerHTML = '<svg class="lc" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';
            rm.addEventListener('click', () => {
                URL.revokeObjectURL(x.url);
                state.files.splice(i, 1);
                syncInputFromState(); renderThumbs(); refresh();
                els.photoInput.focus();
            });
            li.append(img, rm);
            els.thumbs.append(li);
        });
    }
    els.photoInput.addEventListener('change', () => {
        // Input hanya dipakai untuk memilih; daftar final dikelola di state.files.
        const picked = [...els.photoInput.files].filter((f) => !state.files.some((x) => x.file === f));
        addFiles(picked);
    });
    ['dragenter', 'dragover'].forEach((ev) => els.drop.addEventListener(ev, (e) => { e.preventDefault(); els.drop.classList.add('is-over'); }));
    ['dragleave', 'drop'].forEach((ev) => els.drop.addEventListener(ev, (e) => { e.preventDefault(); els.drop.classList.remove('is-over'); }));
    els.drop.addEventListener('drop', (e) => addFiles(e.dataTransfer.files));

    // ----- Tahap 3: lokasi (peta ilustrasi: pin disimpan sebagai persen) -----
    function placePin(x, y) {
        state.pin = { x: Math.min(96, Math.max(4, x)), y: Math.min(96, Math.max(8, y)) };
        syncPin();
    }
    function syncPin() {
        els.pin.hidden = !state.pin;
        if (!state.pin) return;
        els.pin.style.left = state.pin.x + '%';
        els.pin.style.top = state.pin.y + '%';
        els.mapX.value = state.pin.x.toFixed(1);
        els.mapY.value = state.pin.y.toFixed(1);
        els.mapHint.textContent = 'Titik ditandai. Klik lagi untuk memindahkan.';
    }
    els.map.addEventListener('click', (e) => {
        const r = els.map.getBoundingClientRect();
        placePin(((e.clientX - r.left) / r.width) * 100, ((e.clientY - r.top) / r.height) * 100);
    });
    els.map.addEventListener('keydown', (e) => {
        const step = 4, p = state.pin || { x: 50, y: 50 };
        const moves = { ArrowLeft: [-step, 0], ArrowRight: [step, 0], ArrowUp: [0, -step], ArrowDown: [0, step] };
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); placePin(p.x, p.y); }
        else if (moves[e.key]) { e.preventDefault(); placePin(p.x + moves[e.key][0], p.y + moves[e.key][1]); }
    });
    els.address.addEventListener('input', () => {
        const v = els.address.value.trim();
        els.selected.hidden = v.length < 5;
        els.selectedText.textContent = v;
        if (v.length >= 5) clearError('address');
        refresh();
    });

    // ----- Tahap 4: detail -----
    els.title.addEventListener('input', () => { if (els.title.value.trim().length >= 8) clearError('title'); refresh(); });
    els.desc.addEventListener('input', () => {
        els.counter.textContent = `${els.desc.value.length} / 500`;
        if (els.desc.value.trim().length >= 20) clearError('description');
        refresh();
    });

    // ----- Ringkasan di kanan -----
    function refresh() {
        const cat = els.category.find((r) => r.checked);
        const detailOk = isValid(4);
        const rows = {
            category: [Boolean(cat), cat ? cat.dataset.label : null],
            photos: [state.files.length > 0, state.files.length ? `${state.files.length} file` : null],
            address: [isValid(3), els.address.value.trim() || null],
            detail: [detailOk, detailOk ? 'Sudah diisi' : null],
        };
        let done = 0;
        Object.entries(rows).forEach(([key, [ok, text]]) => {
            const li = $(`[data-ad-sum="${key}"]`);
            li.classList.toggle('is-done', ok);
            $('[data-ad-sum-value]', li).textContent = ok ? text : li.dataset.empty;
            if (ok) done += 1;
        });
        els.sumCount.textContent = `${done} dari 4 terisi`;
        els.reviewBtn.disabled = done < 4;
        els.sumHint.textContent = done < 4 ? 'Lengkapi semua tahap untuk melanjutkan.' : 'Semua data terisi. Siap ditinjau.';
        stepEls.forEach((li) => {
            const i = Number(li.dataset.adStep);
            li.classList.toggle('is-done', i < state.step && isValid(i));
        });
    }

    // ----- Tahap 5: tinjau -----
    function fillReview() {
        const cat = els.category.find((r) => r.checked);
        $('[data-ad-rv="category"]').textContent = cat ? cat.dataset.label : '-';
        $('[data-ad-rv="address"]').textContent = els.address.value.trim() + (state.pin ? ' (titik peta ditandai)' : '');
        $('[data-ad-rv="title"]').textContent = els.title.value.trim();
        $('[data-ad-rv="description"]').textContent = els.desc.value.trim();
        const box = $('[data-ad-rv-photos]');
        box.innerHTML = '';
        state.files.forEach((x, i) => {
            const li = document.createElement('li');
            li.className = 'ad-thumb';
            const img = document.createElement('img');
            img.src = x.url; img.alt = `Foto ${i + 1}`;
            li.append(img);
            box.append(li);
        });
    }

    // ----- Kirim (simulasi) -----
    form.addEventListener('submit', (e) => {
        e.preventDefault(); // HAPUS baris ini saat form sudah dihubungkan ke route POST di backend.
        for (let i = 1; i <= 4; i++) {
            if (!isValid(i)) { setStep(i); showErrors(i); return; }
        }
        $('[data-ad-ticket]').textContent = 'CZ-2026-' + String(Math.floor(10000 + Math.random() * 90000));
        root.classList.add('is-done');
        $$('[data-ad-hide-done]').forEach((el) => { el.hidden = true; });
        form.hidden = true;
        els.success.hidden = false;
        els.success.focus();
        els.success.scrollIntoView({ block: 'center', behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    // ----- Buat laporan lain: kosongkan semua -----
    $('[data-ad-reset]').addEventListener('click', () => {
        state.files.forEach((x) => URL.revokeObjectURL(x.url));
        state.files = []; state.pin = null;
        form.reset();
        els.photoInput.value = '';
        renderThumbs(); syncPin();
        els.pin.hidden = true;
        els.mapHint.textContent = 'Klik peta untuk menandai titik';
        els.selected.hidden = true;
        els.counter.textContent = '0 / 500';
        $$('[data-ad-error]').forEach((b) => setError(b.dataset.adError, ''));
        $$('[data-ad-hide-done]').forEach((el) => { el.hidden = false; });
        form.hidden = false; els.success.hidden = true;
        root.classList.remove('is-done');
        refresh(); setStep(1);
    });

    refresh();
    setStep(1, false);
});


// ---------------------------------------------------------------------------
// Aktivitas Saya: tab, filter status, pencarian topbar, daftar + detail laporan,
// timeline, bukti penyelesaian, konfirmasi + rating, dan Riwayat Pulse.
// Hanya aktif di halaman yang memiliki #aktivitas. Data dibaca dari
// <script id="aktivitasData"> (dummy data di Blade; ganti dengan data backend).
// Konfirmasi hanya disimpan di memori (simulasi); sambungkan ke POST saat backend siap.
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('aktivitas');
    const dataEl = document.getElementById('aktivitasData');
    if (!root || !dataEl) return; // halaman lain, atau belum ada laporan (empty state dari Blade)

    const { stages, status: statusMeta, reports } = JSON.parse(dataEl.textContent);
    const $ = (sel, scope = root) => scope.querySelector(sel);
    const $$ = (sel, scope = root) => [...scope.querySelectorAll(sel)];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const list = $('[data-ac-list]');
    const detailEl = $('[data-ac-detail]');
    if (!list || !detailEl) return;

    const byId = new Map(reports.map((r) => [String(r.id), r]));
    const items = $$('[data-ac-item]', list);
    const itemById = new Map(items.map((li) => [li.dataset.id, li]));
    const chips = $$('[data-ac-chip]');
    const tabs = $$('[data-ac-tab]');
    const panels = $$('[data-ac-panel]');
    const state = { tab: 'reports', status: 'semua', q: '', selected: null, draft: {}, editing: new Set() };
    const haystack = (r) => [r.title, r.ticket, r.category_label, r.area, r.city, (statusMeta[r.status] || {}).label].join(' ').toLowerCase();
    const hay = new Map(reports.map((r) => [String(r.id), haystack(r)]));

    // ----- Ikon & helper -----
    const svg = (paths, size = 16, sw = 1.75) => `<svg class="lc" xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="${sw}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">${paths}</svg>`;
    const ICON = {
        check: (s = 14) => svg('<path d="M20 6 9 17l-5-5"/>', s, 2.25),
        alert: (s = 14) => svg('<path d="M12 8v4"/><path d="M12 16h.01"/>', s, 2.25),
        pin: (s = 16) => svg('<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>', s),
        image: (s = 40) => svg('<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>', s, 1.5),
        circleCheck: (s = 18) => svg('<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>', s),
        circleAlert: (s = 18) => svg('<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>', s),
        star: (s = 20) => svg('<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>', s),
    };
    const ICON_CAT = {
        road: '<path d="M4 19 8 5"/><path d="m20 19-4-14"/><path d="M12 5v2"/><path d="M12 11v2"/><path d="M12 17v2"/>',
        lightbulb: '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
        bus: '<path d="M8 6v6"/><path d="M15 6v6"/><path d="M2 12h19.6"/><path d="M18 18h3s.5-1.7.8-2.8c.1-.4.2-.8.2-1.2 0-.4-.1-.8-.2-1.2l-1.4-5C20.1 6.8 19.1 6 18 6H4a2 2 0 0 0-2 2v10h3"/><circle cx="7" cy="18" r="2"/><path d="M9 18h5"/><circle cx="16" cy="18" r="2"/>',
        droplets: '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>',
    };
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const nowLabel = () => {
        const d = new Date();
        return `${d.getDate()} ${MONTHS[d.getMonth()]}, ${String(d.getHours()).padStart(2, '0')}.${String(d.getMinutes()).padStart(2, '0')}`;
    };
    const badge = (r) => {
        const m = statusMeta[r.status] || { label: r.status, tone: 'verified' };
        return `<span class="dash-badge dash-badge--${esc(m.tone)}">${esc(m.label)}</span>`;
    };
    const isDesktop = () => window.matchMedia('(min-width: 992px)').matches;

    // ----- Timeline: status laporan -> 4 tahap (done / current / blocked / todo) -----
    function buildStages(r) {
        const out = stages.map((label, i) => ({ label, i, state: 'todo', date: (r.dates || [])[i] || null, extra: '' }));
        const done = (n) => { for (let i = 0; i < n; i++) out[i].state = 'done'; };
        const pulse = r.pulse;
        switch (r.status) {
            case 'terkirim':
                done(1); out[1].state = 'current'; out[1].extra = 'Menunggu verifikasi petugas'; break;
            case 'terverifikasi':
                done(2); out[2].state = 'current'; out[2].extra = 'Menunggu tindak lanjut lapangan'; break;
            case 'proses':
                done(2); out[2].state = 'current'; out[2].extra = 'Sedang dikerjakan petugas'; break;
            case 'bukti':
                done(2); out[2].state = 'blocked'; out[2].extra = 'Menunggu bukti tambahan dari kamu'; break;
            case 'selesai':
                done(3);
                if (pulse && pulse.verdict === 'sudah') out[3].state = 'done';
                else if (pulse && pulse.verdict === 'belum') { out[3].state = 'blocked'; out[3].extra = 'Kamu menandai belum sesuai, menunggu tindak lanjut'; }
                else { out[3].state = 'current'; out[3].extra = 'Menunggu konfirmasimu'; }
                break;
            case 'ditolak':
                done(1); out[1].state = 'blocked'; out[1].extra = r.note || 'Laporan tidak dapat diproses'; break;
            default: break;
        }
        return out;
    }
    const stageSub = (s) => {
        if (s.state === 'todo') return 'Belum dimulai';
        const parts = [];
        if (s.date) parts.push(esc(s.date));
        if (s.extra) parts.push(`<span class="ac-tl__extra">${esc(s.extra)}</span>`);
        if (!parts.length) parts.push(s.state === 'done' ? 'Selesai' : 'Sedang berlangsung');
        return parts.join(' &middot; ');
    };
    const timelineHtml = (r) => buildStages(r).map((s) => {
        const node = s.state === 'done' ? ICON.check() : s.state === 'blocked' ? ICON.alert() : '';
        return `<li class="ac-tl is-${s.state}"${s.state === 'current' || s.state === 'blocked' ? ' aria-current="step"' : ''}>
            <span class="ac-tl__node">${node}</span>
            <span class="ac-tl__body"><strong>${esc(s.label)}</strong><small>${stageSub(s)}</small></span></li>`;
    }).join('');

    // ----- Bukti penyelesaian -----
    function evidenceHtml(r) {
        const ev = r.evidence;
        if (r.status === 'selesai' && ev) {
            const caption = [ev.caption, ev.officer && !String(ev.caption || '').includes(ev.officer) ? `Petugas: ${ev.officer}` : ''].filter(Boolean).join(' ');
            if (ev.photo) {
                return `<div class="ac-evidence__frame"><span class="ac-evidence__tag">Setelah</span><img src="${esc(ev.photo)}" alt="Foto bukti penyelesaian: ${esc(r.title)}" loading="lazy"></div>
                    <p class="ac-evidence__caption">${esc(caption)}</p>`;
            }
            return `<div class="ac-evidence__frame"><span class="ac-evidence__tag">Setelah</span>${ICON.image(44)}</div>
                <p class="ac-evidence__caption">${esc(caption || 'Foto bukti dari petugas.')} <em>(gambar contoh belum dimuat)</em></p>`;
        }
        const text = r.status === 'ditolak' ? 'Laporan ditolak, tidak ada bukti penyelesaian.' : 'Bukti penyelesaian belum tersedia. Foto dari petugas akan muncul setelah laporan selesai.';
        return `<div class="ac-evidence__frame ac-evidence__frame--empty">${ICON.image(36)}<span class="ac-evidence__empty-text">${text}</span></div>`;
    }

    // ----- Rating bintang -----
    const starsStatic = (n) => `<div class="ac-rating ac-rating--static" role="img" aria-label="Rating ${n} dari 5">${[1, 2, 3, 4, 5].map((v) => `<span class="ac-star${v <= n ? ' is-on' : ''}">${ICON.star(16)}</span>`).join('')}</div>`;
    const starsInput = (value) => `<div class="ac-rating" role="radiogroup" aria-label="Rating Pulse 1 sampai 5" data-ac-rating>${[1, 2, 3, 4, 5].map((v) =>
        `<button type="button" class="ac-star${v <= value ? ' is-on' : ''}" role="radio" aria-checked="${v === value}" aria-label="${v} dari 5" tabindex="${(value ? v === value : v === 1) ? 0 : -1}" data-value="${v}">${ICON.star(20)}</button>`).join('')}</div>`;

    // ----- Konfirmasi penyelesaian (hanya untuk status selesai) -----
    function confirmationHtml(r) {
        if (r.status !== 'selesai') return '';
        const p = r.pulse;
        if (p && !state.editing.has(String(r.id))) {
            const ok = p.verdict === 'sudah';
            return `<section class="ac-confirmation" aria-labelledby="acConfTitle">
                <h3 class="ac-confirmation__title" id="acConfTitle">Konfirmasi penyelesaian</h3>
                <p class="ac-confirmation__hint">Terima kasih sudah memberi Pulse untuk laporan ini.</p>
                <div class="ac-result ${ok ? 'ac-result--ok' : 'ac-result--warn'}" role="status" tabindex="-1" data-ac-result>
                    <p class="ac-result__text">${ok ? ICON.circleCheck() : ICON.circleAlert()}<span>${ok
                        ? 'Konfirmasimu tersimpan: masalah sudah selesai. (Simulasi, belum dikirim ke server.)'
                        : 'Laporan ditandai belum sesuai dan perlu ditindaklanjuti petugas. (Simulasi, belum dikirim ke server.)'}</span></p>
                    <div class="ac-result__foot">${starsStatic(p.rating)}<button type="button" class="ac-link-btn" data-ac-edit>Ubah penilaian</button></div>
                </div></section>`;
        }
        const value = state.draft[r.id] ?? (p ? p.rating : 0);
        return `<section class="ac-confirmation" aria-labelledby="acConfTitle">
            <h3 class="ac-confirmation__title" id="acConfTitle">Konfirmasi penyelesaian</h3>
            <p class="ac-confirmation__hint">Apakah masalah sudah selesai? Beri rating Pulse 1–5.</p>
            ${starsInput(value)}
            <div class="ac-confirmation__actions">
                <button type="button" class="dash-btn dash-btn--primary" data-ac-verdict="sudah">${ICON.circleCheck()}Ya, sudah selesai</button>
                <button type="button" class="dash-btn dash-btn--secondary" data-ac-verdict="belum">Belum sesuai</button>
            </div>
            <p class="ac-feedback ac-feedback--error" role="alert" data-ac-feedback hidden></p>
        </section>`;
    }

    // ----- Render detail -----
    function renderDetail() {
        const r = byId.get(state.selected);
        if (!r) {
            detailEl.innerHTML = `<div class="ac-empty ac-empty--small"><span class="ac-empty__art">${ICON.image(28)}</span><h2 class="ac-empty__title">Pilih laporan</h2><p class="ac-empty__text">Detail dan perkembangan laporan akan tampil di sini.</p></div>`;
            return;
        }
        detailEl.innerHTML = `
            <div class="ac-detail__head">
                <div><p class="ac-detail__ticket">Tiket #${esc(r.ticket)}</p><h2 class="ac-detail__title">${esc(r.title)}</h2></div>
                ${badge(r)}
            </div>
            <div class="ac-detail__meta"><span class="ac-cat">${esc(r.category_label)}</span><span>${ICON.pin()}${esc(r.area)}, ${esc(r.city)}</span></div>
            <div class="ac-detail__cols">
                <div><h3 class="ac-section-title">Linimasa laporan</h3><ol class="ac-timeline">${timelineHtml(r)}</ol></div>
                <div><h3 class="ac-section-title">Bukti penyelesaian</h3>${evidenceHtml(r)}</div>
            </div>
            ${confirmationHtml(r)}`;
    }

    // ----- Pilih laporan -----
    function select(id, { scroll = false } = {}) {
        state.selected = id ? String(id) : null;
        items.forEach((li) => {
            const on = li.dataset.id === state.selected;
            li.querySelector('.ac-card').classList.toggle('is-active', on);
            li.querySelector('.ac-card').setAttribute('aria-pressed', String(on));
        });
        renderDetail();
        if (scroll && !isDesktop()) detailEl.scrollIntoView({ block: 'start', behavior: reduceMotion ? 'auto' : 'smooth' });
    }

    // ----- Filter + pencarian -----
    const emptyEl = $('[data-ac-empty]');
    const countEl = $('[data-ac-count]');
    const matches = (r) => (state.status === 'semua' || r.status === state.status) && (!state.q || hay.get(String(r.id)).includes(state.q));

    function renderList() {
        let shown = 0;
        items.forEach((li) => {
            const ok = matches(byId.get(li.dataset.id));
            li.hidden = !ok;
            if (ok) shown += 1;
        });
        list.hidden = shown === 0;
        emptyEl.hidden = shown > 0;
        countEl.textContent = `Menampilkan ${shown} dari ${reports.length} laporan`;
        if (!shown) {
            const bits = [];
            if (state.status !== 'semua') bits.push(`status "${(statusMeta[state.status] || {}).label || state.status}"`);
            if (state.q) bits.push(`kata kunci "${state.q}"`);
            $('[data-ac-empty-text]').textContent = bits.length ? `Tidak ada laporan dengan ${bits.join(' dan ')}.` : 'Coba ganti filter atau kata kunci pencarian.';
        }
        const first = items.find((li) => !li.hidden);
        if (!state.selected || itemById.get(state.selected).hidden) select(first ? first.dataset.id : null);
    }

    function setChip(value) {
        state.status = value;
        chips.forEach((c) => {
            const on = c.dataset.acChip === value;
            c.classList.toggle('is-active', on);
            c.setAttribute('aria-pressed', String(on));
        });
    }
    chips.forEach((c) => c.addEventListener('click', () => { setChip(c.dataset.acChip); renderList(); }));

    // Pencarian memakai kolom cari di topbar (tidak ada search bar ganda).
    const topInput = document.querySelector('.cz-search__input');
    if (topInput) {
        const fromUrl = new URLSearchParams(window.location.search).get('q');
        if (fromUrl) { topInput.value = fromUrl; state.q = fromUrl.trim().toLowerCase(); }
        topInput.addEventListener('input', () => { state.q = topInput.value.trim().toLowerCase(); renderList(); renderPulse(); });
        topInput.closest('form')?.addEventListener('submit', (e) => e.preventDefault());
    }
    $('[data-ac-reset]').addEventListener('click', () => {
        setChip('semua');
        state.q = '';
        if (topInput) topInput.value = '';
        renderList(); renderPulse();
    });

    list.addEventListener('click', (e) => {
        const li = e.target.closest('[data-ac-item]');
        if (li) select(li.dataset.id, { scroll: true });
    });

    // ----- Interaksi di panel detail: rating, konfirmasi, ubah -----
    const paintStars = (group, value) => {
        $$('.ac-star', group).forEach((b) => {
            const v = Number(b.dataset.value);
            b.classList.toggle('is-on', v <= value);
        });
    };
    const setRating = (group, value, { focus = false } = {}) => {
        state.draft[state.selected] = value;
        $$('.ac-star', group).forEach((b) => {
            const v = Number(b.dataset.value);
            b.setAttribute('aria-checked', String(v === value));
            b.tabIndex = v === value ? 0 : -1;
            if (focus && v === value) b.focus();
        });
        paintStars(group, value);
        const fb = $('[data-ac-feedback]', detailEl);
        if (fb) fb.hidden = true;
    };
    const draftValue = () => state.draft[state.selected] || 0;

    detailEl.addEventListener('click', (e) => {
        const star = e.target.closest('[data-ac-rating] .ac-star');
        if (star) { setRating(star.closest('[data-ac-rating]'), Number(star.dataset.value)); return; }
        const verdictBtn = e.target.closest('[data-ac-verdict]');
        if (verdictBtn) { submitVerdict(verdictBtn.dataset.acVerdict); return; }
        if (e.target.closest('[data-ac-edit]')) {
            const r = byId.get(state.selected);
            state.editing.add(String(r.id));
            state.draft[r.id] = r.pulse ? r.pulse.rating : 0;
            renderDetail();
            $('.ac-star[tabindex="0"]', detailEl)?.focus();
        }
    });
    // Hover: pratinjau bintang; kembali ke nilai terpilih saat mouse keluar.
    detailEl.addEventListener('mouseover', (e) => {
        const star = e.target.closest('[data-ac-rating] .ac-star');
        if (star) paintStars(star.closest('[data-ac-rating]'), Number(star.dataset.value));
    });
    detailEl.addEventListener('mouseout', (e) => {
        const group = e.target.closest('[data-ac-rating]');
        if (group && !group.contains(e.relatedTarget)) paintStars(group, draftValue());
    });
    // Keyboard: panah mengubah rating (pola radiogroup).
    detailEl.addEventListener('keydown', (e) => {
        const star = e.target.closest('[data-ac-rating] .ac-star');
        if (!star) return;
        const cur = Number(star.dataset.value);
        const delta = { ArrowRight: 1, ArrowUp: 1, ArrowLeft: -1, ArrowDown: -1 }[e.key];
        if (delta) { e.preventDefault(); setRating(star.closest('[data-ac-rating]'), Math.min(5, Math.max(1, cur + delta)), { focus: true }); }
        else if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setRating(star.closest('[data-ac-rating]'), cur); }
    });

    function submitVerdict(verdict) {
        const r = byId.get(state.selected);
        if (!r || r.status !== 'selesai') return;
        const rating = draftValue();
        const fb = $('[data-ac-feedback]', detailEl);
        if (!rating) {
            fb.innerHTML = `${ICON.circleAlert(16)}<span>Pilih rating bintang 1–5 dulu sebelum mengirim konfirmasi.</span>`;
            fb.hidden = false;
            $('.ac-star[tabindex="0"]', detailEl)?.focus();
            return;
        }
        const buttons = $$('[data-ac-verdict]', detailEl);
        buttons.forEach((b) => { b.disabled = true; });
        // Simulasi menyimpan; ganti dengan fetch/POST ke backend.
        setTimeout(() => {
            r.pulse = { verdict, rating, date: nowLabel(), comment: r.pulse ? r.pulse.comment : '' };
            state.editing.delete(String(r.id));
            delete state.draft[r.id];
            renderDetail();
            renderPulse();
            $('[data-ac-result]', detailEl)?.focus();
        }, reduceMotion ? 0 : 450);
    }

    // ----- Riwayat Pulse -----
    const pulseList = $('[data-ac-pulse-list]');
    const pulseEmpty = $('[data-ac-pulse-empty]');
    const pulseCount = $('[data-ac-pulse-count]');
    function renderPulse() {
        const all = reports.filter((r) => r.pulse);
        pulseCount.textContent = all.length;
        const rows = all.filter((r) => !state.q || hay.get(String(r.id)).includes(state.q));
        pulseList.innerHTML = rows.map((r) => {
            const p = r.pulse, ok = p.verdict === 'sudah';
            return `<li class="dash-card ac-pulse-item">
                <div class="ac-pulse-item__top">
                    <div><span class="ac-cat">${esc(r.category_label)}</span>
                        <h3 class="ac-pulse-item__title">${esc(r.title)}</h3>
                        <p class="ac-pulse-item__sub">#${esc(r.ticket)} &middot; ${esc(p.date)}</p></div>
                    <span class="dash-badge dash-badge--${ok ? 'done' : 'rejected'}">${ok ? 'Sudah selesai' : 'Belum sesuai'}</span>
                </div>
                ${p.comment ? `<p class="ac-pulse-item__comment">&ldquo;${esc(p.comment)}&rdquo;</p>` : ''}
                <div class="ac-pulse-item__foot">
                    <span class="ac-pulse-item__rating">${starsStatic(p.rating)}${p.rating}/5</span>
                    <button type="button" class="ac-link-btn" data-ac-open-report="${esc(r.id)}">Lihat laporan</button>
                </div></li>`;
        }).join('');
        pulseList.hidden = rows.length === 0;
        pulseEmpty.hidden = rows.length > 0;
        if (!rows.length) {
            $('[data-ac-pulse-empty-text]').textContent = all.length
                ? 'Tidak ada riwayat Pulse yang cocok dengan pencarian.'
                : 'Setelah laporanmu selesai, kamu bisa mengonfirmasi dan memberi rating. Riwayatnya muncul di sini.';
        }
    }
    pulseList.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-ac-open-report]');
        if (!btn) return;
        setTab('reports', { focus: false });
        // Pastikan laporan terlihat meski filter sedang aktif.
        if (itemById.get(btn.dataset.acOpenReport).hidden) { setChip('semua'); state.q = ''; if (topInput) topInput.value = ''; renderList(); }
        select(btn.dataset.acOpenReport, { scroll: true });
    });

    // ----- Tab -----
    function setTab(name, { focus = true } = {}) {
        state.tab = name;
        tabs.forEach((t) => {
            const on = t.dataset.acTab === name;
            t.setAttribute('aria-selected', String(on));
            t.tabIndex = on ? 0 : -1;
            if (on && focus) t.focus();
        });
        panels.forEach((p) => { p.hidden = p.dataset.acPanel !== name; });
    }
    tabs.forEach((t, i) => {
        t.addEventListener('click', () => setTab(t.dataset.acTab, { focus: false }));
        t.addEventListener('keydown', (e) => {
            const move = { ArrowRight: 1, ArrowLeft: -1 }[e.key];
            if (move) { e.preventDefault(); setTab(tabs[(i + move + tabs.length) % tabs.length].dataset.acTab); }
            else if (e.key === 'Home') { e.preventDefault(); setTab(tabs[0].dataset.acTab); }
            else if (e.key === 'End') { e.preventDefault(); setTab(tabs[tabs.length - 1].dataset.acTab); }
        });
    });

    renderPulse();
    renderList(); // memilih laporan pertama + menggambar detail
});

// ---------------------------------------------------------------------------
// Preferensi tampilan: dipasang di SEMUA halaman dashboard (bukan hanya Profil).
// Hanya preferensi (tema, ukuran teks, toggle) yang disimpan di browser;
// data pribadi (nama, email, kata sandi) tidak disimpan di sini.
// Ganti dengan data dari backend saat tersedia.
// ---------------------------------------------------------------------------
const CZ_PREFS_KEY = 'cityzen.prefs';
const czReadPrefs = () => {
    try { return JSON.parse(localStorage.getItem(CZ_PREFS_KEY)) || {}; } catch (e) { return {}; }
};
const czApplyPrefs = (prefs) => {
    document.documentElement.dataset.theme = prefs.theme || 'terang'; // pengait untuk tema gelap nanti
    document.body.classList.toggle('cz-text-lg', prefs.text_size === 'besar');
};
document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('cz-dashboard')) czApplyPrefs(czReadPrefs());
});

// ---------------------------------------------------------------------------
// Profil: edit info pribadi, preferensi, ubah kata sandi, keluar.
// Hanya aktif di halaman yang memiliki #profil.
// ---------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('profil');
    if (!root) return;

    const { user, settings } = JSON.parse(document.getElementById('profilData').textContent);
    const $ = (sel, scope = root) => scope.querySelector(sel);
    const $$ = (sel, scope = root) => [...scope.querySelectorAll(sel)];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ----- Notifikasi singkat -----
    const toastEl = $('[data-pf-toast]');
    let toastTimer;
    function toast(message, type = 'ok') {
        toastEl.className = 'pf-toast pf-toast--' + type;
        $('[data-pf-toast-text]', toastEl).textContent = message;
        toastEl.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { toastEl.hidden = true; }, 3800);
    }

    const setError = (field, message) => {
        const box = $(`[data-pf-error="${field}"]`);
        if (!box) return;
        box.hidden = !message;
        $('span', box).textContent = message || '';
        box.closest('.ad-field')?.classList.toggle('has-error', Boolean(message));
    };
    const initialsOf = (name) => name.trim().split(/\s+/).slice(0, 2).map((w) => w[0]?.toUpperCase() || '').join('');

    // =======================================================================
    // 1) Info pribadi
    // =======================================================================
    const form = $('#profilForm');
    const fields = { name: $('#pfName'), username: $('#pfUsername'), email: $('#pfEmail'), phone: $('#pfPhone'), district: $('#pfDistrict') };
    const saveBtn = $('[data-pf-save]'), cancelBtn = $('[data-pf-cancel]');
    const saved = { name: user.name, username: user.username, email: user.email, phone: user.phone, district: user.district };

    const rules = {
        name: (v) => (v.trim().length >= 3 ? '' : 'Nama lengkap minimal 3 karakter.'),
        username: (v) => (/^[a-z0-9_]{3,20}$/i.test(v.trim()) ? '' : 'Nama pengguna 3 sampai 20 karakter: huruf, angka, atau garis bawah.'),
        email: (v) => (/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Format email belum benar. Contoh: nama@email.com'),
        phone: (v) => (/^(\+62|62|0)8\d{8,11}$/.test(v.replace(/[\s-]/g, '')) ? '' : 'Nomor ponsel belum benar. Contoh: 0812 3456 7890'),
        district: (v) => (v.trim().length >= 3 ? '' : 'Isi kecamatan domisili.'),
    };
    const isDirty = () => Object.keys(fields).some((k) => fields[k].value.trim() !== saved[k]);
    const syncButtons = () => { const d = isDirty(); saveBtn.disabled = !d; cancelBtn.disabled = !d; };

    Object.entries(fields).forEach(([key, input]) => {
        input.addEventListener('input', () => {
            if (!$(`[data-pf-error="${key}"]`).hidden) setError(key, rules[key](input.value));
            syncButtons();
        });
    });

    cancelBtn.addEventListener('click', () => {
        Object.entries(fields).forEach(([key, input]) => { input.value = saved[key]; setError(key, ''); });
        syncButtons();
    });

    const photoState = { url: null };
    function paintIdentity() {
        $('[data-pf-name]').textContent = saved.name;
        $('[data-pf-username]').textContent = saved.username;
        $('[data-pf-district]').textContent = saved.district;
        const ini = initialsOf(saved.name);
        if (!photoState.url) $('[data-pf-avatar-text]').textContent = ini;
        // Perbarui avatar/nama yang sama di topbar & sidebar agar konsisten.
        const topAvatar = document.querySelector('.cz-topbar__avatar');
        if (topAvatar) topAvatar.textContent = ini;
        const sbAvatar = document.querySelector('.cz-sidebar__avatar');
        if (sbAvatar) sbAvatar.textContent = ini;
        const sbName = document.querySelector('.cz-sidebar__user-text strong');
        if (sbName) sbName.textContent = saved.name;
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault(); // HAPUS baris ini saat form dihubungkan ke route PATCH di backend.
        let firstBad = null;
        Object.entries(fields).forEach(([key, input]) => {
            const msg = rules[key](input.value);
            setError(key, msg);
            if (msg && !firstBad) firstBad = input;
        });
        if (firstBad) { firstBad.focus(); toast('Periksa kembali isian yang ditandai.', 'err'); return; }
        Object.keys(fields).forEach((k) => { saved[k] = fields[k].value.trim(); fields[k].value = saved[k]; });
        paintIdentity();
        syncButtons();
        toast('Perubahan disimpan.');
    });

    // Foto profil: pratinjau lokal saja (belum diunggah ke server).
    $('#pfPhoto').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 2 * 1024 * 1024) {
            toast('Foto harus JPG atau PNG, maksimal 2 MB.', 'err');
            e.target.value = '';
            return;
        }
        if (photoState.url) URL.revokeObjectURL(photoState.url);
        photoState.url = URL.createObjectURL(file);
        const avatar = $('[data-pf-avatar]');
        avatar.innerHTML = '';
        const img = document.createElement('img');
        img.src = photoState.url; img.alt = 'Foto profil';
        avatar.append(img);
        toast('Foto profil diperbarui.');
    });

    // =======================================================================
    // 2) Tampilan & pengaturan (tersimpan langsung, tanpa tombol simpan)
    // =======================================================================
    const prefs = { ...settings, ...czReadPrefs() };
    const persist = () => { try { localStorage.setItem(CZ_PREFS_KEY, JSON.stringify(prefs)); } catch (e) { /* penyimpanan browser tidak tersedia */ } };

    const themeNote = $('[data-pf-theme-note]');
    function paintPrefs() {
        $$('input[data-pf-pref]').forEach((r) => { r.checked = String(prefs[r.dataset.pfPref]) === r.value; });
        $$('[data-pf-switch]').forEach((s) => s.setAttribute('aria-checked', String(Boolean(prefs[s.dataset.pfSwitch]))));
        themeNote.hidden = prefs.theme === 'terang';
        czApplyPrefs(prefs);
    }
    $$('input[data-pf-pref]').forEach((r) => r.addEventListener('change', () => {
        prefs[r.dataset.pfPref] = r.value;
        persist(); paintPrefs(); toast('Pengaturan disimpan.');
    }));
    $$('[data-pf-switch]').forEach((s) => s.addEventListener('click', () => {
        prefs[s.dataset.pfSwitch] = !prefs[s.dataset.pfSwitch];
        persist(); paintPrefs(); toast('Pengaturan disimpan.');
    }));
    paintPrefs();

    // =======================================================================
    // 3) Ubah kata sandi (simulasi: tidak dikirim dan tidak disimpan)
    // =======================================================================
    const pwModalEl = $('#pfPasswordModal');
    const pwForm = $('#pfPasswordForm');
    const pw = { current: $('#pfPw_current'), next: $('#pfPw_next'), confirm: $('#pfPw_confirm') };
    const pwRules = {
        current: (v) => (v ? '' : 'Isi kata sandi saat ini.'),
        next: (v) => (v.length < 8 ? 'Kata sandi baru minimal 8 karakter.'
            : !/[A-Za-z]/.test(v) || !/\d/.test(v) ? 'Gunakan kombinasi huruf dan angka.'
            : v === pw.current.value ? 'Kata sandi baru harus berbeda dari yang lama.' : ''),
        confirm: (v) => (v === pw.next.value && v ? '' : 'Kata sandi belum sama.'),
    };
    pwForm.addEventListener('submit', (e) => {
        e.preventDefault(); // HAPUS baris ini saat form dihubungkan ke route kata sandi di backend.
        let firstBad = null;
        Object.entries(pw).forEach(([key, input]) => {
            const msg = pwRules[key](input.value);
            setError('pw_' + key, msg);
            if (msg && !firstBad) firstBad = input;
        });
        if (firstBad) { firstBad.focus(); return; }
        window.bootstrap?.Modal.getOrCreateInstance(pwModalEl).hide();
        toast('Isian kata sandi valid. Belum terhubung ke server, jadi belum diubah.');
    });
    Object.entries(pw).forEach(([key, input]) => input.addEventListener('input', () => {
        if (!$(`[data-pf-error="pw_${key}"]`).hidden) setError('pw_' + key, pwRules[key](input.value));
    }));
    pwModalEl.addEventListener('hidden.bs.modal', () => {
        pwForm.reset();
        Object.keys(pw).forEach((k) => setError('pw_' + k, ''));
        $$('.password-toggle', pwModalEl).forEach((b) => { // kembalikan ke mode tersembunyi
            const input = b.closest('.password-field').querySelector('input');
            input.type = 'password';
            b.querySelector('i').className = 'bi bi-eye';
        });
    });
    pwModalEl.addEventListener('shown.bs.modal', () => pw.current.focus());

    // =======================================================================
    // 4) Keluar (simulasi: sesi belum diakhiri di server)
    // =======================================================================
    $('[data-pf-logout]').addEventListener('click', () => {
        window.bootstrap?.Modal.getOrCreateInstance($('#pfLogoutModal')).hide();
        toast('Mode contoh: sesi belum diakhiri di server. Mengarahkan ke beranda...');
        setTimeout(() => { window.location.href = root.dataset.logoutUrl; }, reduceMotion ? 800 : 1400); // ganti dengan POST logout saat backend siap
    });
});