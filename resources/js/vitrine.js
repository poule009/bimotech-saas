/*
| JS du site vitrine (marketing) — autonome, sans Alpine.
| Nav au scroll, reveal accessible (respecte prefers-reduced-motion),
| accordéon FAQ, menu mobile (burger).
*/

// ── Nav : état opaque au scroll ──────────────────────────────────────────
const nav = document.getElementById('nav');
if (nav) {
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// ── Reveal au scroll (désactivé si l'utilisateur réduit les animations) ──
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const reveals = document.querySelectorAll('.reveal');
if (prefersReduced || ! ('IntersectionObserver' in window)) {
    reveals.forEach((el) => el.classList.add('in'));
} else {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.12 });
    reveals.forEach((el) => io.observe(el));
}

// ── FAQ : un seul item ouvert à la fois ──────────────────────────────────
document.querySelectorAll('.faq-item').forEach((item) => {
    const q = item.querySelector('.faq-q');
    const a = item.querySelector('.faq-a');
    if (! q || ! a) return;
    q.addEventListener('click', () => {
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach((o) => {
            o.classList.remove('open');
            const oa = o.querySelector('.faq-a');
            if (oa) oa.style.maxHeight = null;
        });
        if (! isOpen) {
            item.classList.add('open');
            a.style.maxHeight = a.scrollHeight + 'px';
        }
    });
});

// ── Menu mobile (burger) ─────────────────────────────────────────────────
const burger = document.querySelector('.nav-burger');
const mobileMenu = document.getElementById('mobile-menu');
if (burger && mobileMenu) {
    const toggleMenu = (force) => {
        const open = typeof force === 'boolean' ? force : ! mobileMenu.classList.contains('open');
        mobileMenu.classList.toggle('open', open);
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.style.overflow = open ? 'hidden' : '';
    };
    burger.addEventListener('click', () => toggleMenu());
    mobileMenu.querySelectorAll('a').forEach((link) =>
        link.addEventListener('click', () => toggleMenu(false)));
}
