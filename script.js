// ═══════════════════════════════════════════
//  AASH.DEV — script.js (FIXED & OPTIMIZED)
// ═══════════════════════════════════════════

// 1. LOADER
(function () {
    const bar = document.getElementById('loaderBar');
    const pct = document.getElementById('loaderPct');
    const loader = document.getElementById('loader');
    let p = 0;
    const interval = setInterval(() => {
        p += Math.random() * 8 + 2;
        if (p >= 100) { p = 100; clearInterval(interval); }
        if (bar) bar.style.width = p + '%';
        if (pct) pct.textContent = Math.round(p) + '%';
        if (p >= 100) {
            setTimeout(() => {
                if (loader) loader.classList.add('out');
                setTimeout(() => {
                    if (loader) loader.style.display = 'none';
                    setTimeout(startTyping, 300);
                }, 600);
            }, 300);
        }
    }, 40);
})();

// 2. TYPING EFFECT
const words = ['Junior Developer', 'UI Designer', , 'Problem Solver', 'Freelancer'];
let wi = 0, ci = 0, del = false;
function startTyping() {
    const el = document.getElementById('typingText');
    if (!el) return;
    function tick() {
        const w = words[wi];
        el.textContent = del ? w.substring(0, --ci) : w.substring(0, ++ci);
        if (!del && ci === w.length) { del = true; setTimeout(tick, 1800); return; }
        if (del && ci === 0) { del = false; wi = (wi + 1) % words.length; }
        setTimeout(tick, del ? 55 : 110);
    }
    tick();
}

// 3. COUNTER ANIMATION (Fixed: Triggers only when visible on screen)
const countObserver = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.getAttribute('data-count'));
            let cur = 0;
            const step = Math.max(1, Math.ceil(target / 40));
            const t = setInterval(() => {
                cur = Math.min(cur + step, target);
                el.textContent = cur;
                if (cur >= target) clearInterval(t);
            }, 30);
            obs.unobserve(el); // Stop observing once counted
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.snum[data-count]').forEach(el => countObserver.observe(el));


// 4. NAVBAR SCROLL
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar?.classList.toggle('scrolled', window.scrollY > 60);
    updateScrollBar();
    updateActiveNav();
});

// 5. SCROLL PROGRESS BAR
function updateScrollBar() {
    const bar = document.getElementById('scrollBar');
    if (!bar) return;
    // Cross-browser scroll calculation
    const scrollPx = document.documentElement.scrollTop || document.body.scrollTop;
    const winHeightPx = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const pct = (scrollPx / winHeightPx) * 100;
    bar.style.width = pct + '%';
}

// 6. MOBILE MENU (Fixed: Bound to window for inline HTML access)
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
hamburger?.addEventListener('click', () => mobileMenu?.classList.toggle('active'));

window.closeMobile = function () {
    mobileMenu?.classList.remove('active', 'open');
};

document.addEventListener('click', e => {
    if (!hamburger?.contains(e.target) && !mobileMenu?.contains(e.target)) {
        mobileMenu?.classList.remove('active', 'open');
    }
});

// 7. REVEAL ON SCROLL (Fixed: Removed the 2000ms delay that broke the scroll effect)
const revealObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible', 'active');
            e.target.querySelectorAll('.skill-fill').forEach(f => {
                f.style.width = (f.getAttribute('data-w') || 0) + '%';
            });
            revealObs.unobserve(e.target);
        }
    });
}, { threshold: 0.10, rootMargin: '0px 0px -50px 0px' });

document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

// 8. ACTIVE NAV LINK
function updateActiveNav() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-links a');
    let current = '';
    sections.forEach(s => {
        if (window.scrollY >= s.offsetTop - 150) current = s.id;
    });
    navLinks.forEach(a => {
        a.classList.remove('active-nav');
        if (a.getAttribute('href') === '#' + current) a.classList.add('active-nav');
    });
}

// 9. SMOOTH SCROLL (Fixed: Prevents crash when clicking empty href="#")
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href === '#') {
            e.preventDefault();
            return;
        }
        const t = document.querySelector(href);
        if (t) {
            e.preventDefault();
            t.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// 10. CARD TILT
document.querySelectorAll('.proj-card, .skill-card, .svc-card').forEach(card => {
    card.addEventListener('mousemove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - 0.5;
        const y = (e.clientY - r.top) / r.height - 0.5;
        card.style.transform = `perspective(700px) rotateY(${x * 5}deg) rotateX(${-y * 5}deg) translateY(-6px)`;
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
        card.style.transition = 'transform 0.4s ease';
    });
    card.addEventListener('mouseenter', () => {
        card.style.transition = 'none'; // Snappier interaction
    });
});

// 11. FLYWAYS MODAL (Fixed: Bulletproof closing logic)
window.openFlyways = function () {
    const modal = document.getElementById('flywaysModal');
    if (modal) { modal.classList.add('open', 'show'); document.body.style.overflow = 'hidden'; }
}

window.closeFlyways = function (e) {
    // Only close if they clicked the background, or specifically the close buttons
    if (e && e.target.id !== 'flywaysModal' && !e.target.closest('.modal-close') && !e.target.classList.contains('btn-ghost')) {
        return;
    }
    const modal = document.getElementById('flywaysModal');
    if (modal) { modal.classList.remove('open', 'show'); document.body.style.overflow = ''; }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeFlyways();
    }
});

// 12. CONTACT FORM (Fixed: Form messages now properly display)
const contactForm = document.getElementById('contactForm');
const submitBtn = document.getElementById('submitBtn');
const btnText = document.getElementById('btnText');
const btnLoader = document.getElementById('btnLoader');
const formMsg = document.getElementById('formMsg');

if (contactForm) {
    contactForm.addEventListener('submit', async e => {
        e.preventDefault();

        // UI Loading State
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        submitBtn.disabled = true;

        // Clear previous messages
        if (formMsg) {
            formMsg.className = 'form-msg';
            formMsg.textContent = '';
            formMsg.style.display = 'block';
        }

        try {
            const res = await fetch('php/contact.php', { method: 'POST', body: new FormData(contactForm) });
            const data = await res.json();

            if (data.success) {
                submitBtn.classList.add('success');
                if (formMsg) {
                    formMsg.className = 'form-msg success';
                    formMsg.textContent = '✓ ' + data.message;
                    formMsg.style.color = '#22c55e'; // Green
                }
                contactForm.reset();
                setTimeout(() => {
                    submitBtn.classList.remove('success');
                    if (formMsg) formMsg.textContent = '';
                }, 5000);
            } else {
                submitBtn.classList.add('errstate');
                if (formMsg) {
                    formMsg.className = 'form-msg error';
                    formMsg.textContent = '✕ ' + data.message;
                    formMsg.style.color = '#ef4444'; // Red
                }
                setTimeout(() => submitBtn.classList.remove('errstate'), 2000);
            }
        } catch {
            submitBtn.classList.add('errstate');
            if (formMsg) {
                formMsg.className = 'form-msg error';
                formMsg.textContent = '✕ Could not connect. Please WhatsApp me: +94 767 901 679';
                formMsg.style.color = '#ef4444';
            }
            setTimeout(() => submitBtn.classList.remove('errstate'), 3000);
        }

        // Restore UI State
        btnText.style.display = 'inline-block';
        btnLoader.style.display = 'none';
        submitBtn.disabled = false;
    });
}