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