// checkout.js - JavaScript Halaman Checkout

document.addEventListener('DOMContentLoaded', function () {

    // ── Validasi promo code ───────────────────────────────────
    const promoBtn = document.getElementById('apply-promo');
    if (promoBtn) {
        promoBtn.addEventListener('click', function () {
            const kode = document.getElementById('promo_code')?.value?.trim();
            if (!kode) return;
            fetch('checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'apply_promo=1&promo_code=' + encodeURIComponent(kode)
            })
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('promo-result');
                if (!el) return;
                if (data.success) {
                    el.innerHTML = `<span style="color:green;"><i class="fas fa-check-circle"></i> Promo diterapkan! Diskon ${data.diskon_text}</span>`;
                    const discountEl = document.getElementById('discount-amount');
                    if (discountEl) discountEl.textContent = '- ' + data.diskon_text;
                    const totalEl = document.getElementById('grand-total');
                    if (totalEl) totalEl.textContent = data.total_text;
                } else {
                    el.innerHTML = `<span style="color:red;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                }
            })
            .catch(() => {});
        });
    }

    // ── Pilih metode pembayaran ───────────────────────────────
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            const input = this.querySelector('input[type="radio"]');
            if (input) input.checked = true;

            // Tampilkan info bank yang sesuai
            document.querySelectorAll('.bank-info').forEach(b => b.style.display = 'none');
            const method = input?.value;
            const infoEl = document.getElementById('info-' + method);
            if (infoEl) infoEl.style.display = 'block';
        });
    });

    // ── Pilih kurir ───────────────────────────────────────────
    document.querySelectorAll('.kurir-option').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.kurir-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            const input = this.querySelector('input[type="radio"]');
            if (input) input.checked = true;
        });
    });

    // ── Konfirmasi sebelum submit ─────────────────────────────
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function (e) {
            const metode = document.querySelector('input[name="metode_bayar"]:checked');
            const kurir  = document.querySelector('input[name="kurir"]:checked');
            const alamat = document.querySelector('select[name="address_id"]') || document.getElementById('address_id');
            if (!metode) { e.preventDefault(); alert('Pilih metode pembayaran terlebih dahulu.'); return; }
            if (!kurir)  { e.preventDefault(); alert('Pilih kurir pengiriman terlebih dahulu.'); return; }
        });
    }
});
