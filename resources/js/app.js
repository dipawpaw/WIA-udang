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