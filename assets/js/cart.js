// cart.js - JavaScript Keranjang Belanja

document.addEventListener('DOMContentLoaded', function () {

    // ── Update qty via input ──────────────────────────────────
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function () {
            const pid = this.dataset.id;
            const qty = Math.max(1, parseInt(this.value) || 1);
            this.value = qty;
            updateCart(pid, qty);
        });
    });

    // ── Qty buttons +/- ──────────────────────────────────────
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.closest('.qty-control')?.querySelector('.qty-input');
            if (!input) return;
            const current = parseInt(input.value) || 1;
            const newVal = this.dataset.action === 'plus' ? current + 1 : Math.max(1, current - 1);
            input.value = newVal;
            updateCart(input.dataset.id, newVal);
        });
    });

    // ── Remove item ───────────────────────────────────────────
    document.querySelectorAll('.cart-remove').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!confirm('Hapus produk ini dari keranjang?')) return;
            const pid = this.dataset.id;
            fetch(`cart.php?remove=${pid}`, { method: 'GET' })
                .then(() => this.closest('tr, .cart-item')?.remove())
                .then(() => recalcTotal());
        });
    });

    function updateCart(pid, qty) {
        fetch(`cart.php?update=${pid}&qty=${qty}`)
            .then(res => res.json())
            .then(data => {
                if (data.item_total !== undefined) {
                    const el = document.querySelector(`.item-total[data-id="${pid}"]`);
                    if (el) el.textContent = 'Rp ' + data.item_total.toLocaleString('id-ID');
                }
                if (data.cart_total !== undefined) {
                    const el = document.querySelector('#cart-total');
                    if (el) el.textContent = 'Rp ' + data.cart_total.toLocaleString('id-ID');
                }
            })
            .catch(() => location.reload());
    }

    function recalcTotal() {
        const totals = [...document.querySelectorAll('.item-subtotal')]
            .map(el => parseInt(el.dataset.value) || 0)
            .reduce((a, b) => a + b, 0);
        const el = document.querySelector('#cart-total');
        if (el) el.textContent = 'Rp ' + totals.toLocaleString('id-ID');
    }
});
