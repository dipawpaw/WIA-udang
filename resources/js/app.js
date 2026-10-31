// Cityzen — interaksi frontend ringan (tanpa framework)

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
            links.forEach((a) => a.classList.toggle('active', a.getAttribute('href') === hash));
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