// main.js - JavaScript Utama Lumière Parfum

document.addEventListener('DOMContentLoaded', function () {

    // ── Navbar scroll effect ──────────────────────────────────
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.scrollY > 50
                ? '0 4px 20px rgba(0,0,0,.3)'
                : '0 2px 10px rgba(0,0,0,.1)';
        });
    }

    // ── Toggle password visibility ────────────────────────────
    document.querySelectorAll('.toggle-pass').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.previousElementSibling;
            if (!input || input.tagName !== 'INPUT') return;
            input.type = input.type === 'password' ? 'text' : 'password';
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });

    // ── Auto-dismiss alert messages ───────────────────────────
    document.querySelectorAll('.auth-success, .auth-error').forEach(el => {
        setTimeout(() => el.style.opacity = '0', 4000);
        setTimeout(() => el.remove(), 4500);
    });

    // ── Smooth scroll to anchor ───────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── Lazy load images ──────────────────────────────────────
    if ('IntersectionObserver' in window) {
        const imgObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    imgObserver.unobserve(img);
                }
            });
        });
        document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
    }

    // ── Wishlist toggle via AJAX ──────────────────────────────
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pid = this.dataset.id;
            fetch('product-detail.php?id=' + pid, {
                method: 'POST',
                body: new URLSearchParams({ toggle_wishlist: 1 })
            }).then(() => {
                this.classList.toggle('active');
                const icon = this.querySelector('i');
                if (icon) icon.classList.toggle('fas');
            });
        });
    });

    // ── Back to top button ────────────────────────────────────
    const backTop = document.createElement('button');
    backTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backTop.style.cssText = 'position:fixed;bottom:24px;right:24px;width:44px;height:44px;background:var(--gold);color:#fff;border:none;border-radius:50%;cursor:pointer;font-size:1rem;display:none;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,.2);';
    document.body.appendChild(backTop);
    window.addEventListener('scroll', () => {
        backTop.style.display = window.scrollY > 400 ? 'flex' : 'none';
        backTop.style.alignItems = 'center';
        backTop.style.justifyContent = 'center';
    });
    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
});
