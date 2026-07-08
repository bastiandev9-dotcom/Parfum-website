# 🌸 Lumière Parfum

Toko parfum premium berbasis web yang dibangun dengan PHP native dan MySQL. Menyediakan pengalaman belanja parfum yang elegan dengan fitur lengkap mulai dari katalog produk, keranjang belanja, checkout, pelacakan pesanan, hingga panel admin yang komprehensif.

---

## 📋 Daftar Isi

- [Teknologi](#teknologi)
- [Fitur](#fitur)
- [Struktur Direktori](#struktur-direktori)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Setup & Instalasi](#setup--instalasi)
- [Akun Demo](#akun-demo)
- [Skema Database](#skema-database)
- [Alur Penggunaan](#alur-penggunaan)

---

## 🛠 Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.x (native, tanpa framework) |
| Database | MySQL 5.7+ / MariaDB 10.x |
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Icon | Font Awesome 6.4 |
| Server | Apache (XAMPP / Laragon) |

---

## ✨ Fitur

### 🛍️ Toko (Customer)

- **Beranda** — Banner hero, koleksi best seller, produk unggulan
- **Katalog Produk** — Filter berdasarkan kategori, brand, gender & harga; sorting; pencarian
- **Detail Produk** — Galeri foto, varian ukuran/konsentrasi, review & rating pelanggan
- **Keranjang Belanja** — Tambah/hapus/update qty, total otomatis, persist via session
- **Wishlist** — Simpan produk favorit untuk dibeli nanti
- **Checkout** — Pilih alamat tersimpan atau isi baru, pilih kurir (JNE / J&T / SiCepat / AnterAja), kode promo, metode pembayaran (Transfer BCA, BNI, COD)
- **Lacak Pesanan** — Cek status pesanan via kode order tanpa perlu login
- **Halaman Statis** — FAQ, Tentang Kami, Kebijakan Privasi, Syarat & Ketentuan, Kontak

### 👤 Panel Customer (Login Required)

- **Dashboard** — Ringkasan pesanan aktif, riwayat pesanan, total pengeluaran
- **Riwayat Pesanan** — Daftar semua pesanan beserta status
- **Detail Pesanan** — Timeline status, detail item, info pengiriman & resi
- **Profil** — Edit nama, email, nomor telepon
- **Ganti Password** — Verifikasi password lama sebelum ubah ke baru
- **Alamat** — Tambah/edit/hapus beberapa alamat pengiriman
- **Wishlist** — Kelola daftar produk favorit

### 🔐 Autentikasi

- **Registrasi** — Daftar akun baru dengan validasi email unik
- **Login** — Autentikasi dengan bcrypt (`password_verify`)
- **Lupa Password** — Generate token reset (berlaku 1 jam), link reset ditampilkan (siap integrasi email)
- **Reset Password** — Ganti password via token, token hangus setelah dipakai
- **Logout** — Hapus session

### ⚙️ Panel Admin

- **Dashboard** — Statistik penjualan, jumlah order, produk aktif, total customer; grafik penjualan bulanan; produk terlaris; pesanan terbaru
- **Manajemen Pesanan** — Lihat semua pesanan, update status (pending → diproses → dikirim → selesai / batal), auto-generate nomor resi, filter & pencarian
- **Detail Pesanan** — Info lengkap pesanan, item, alamat, log perubahan status
- **Manajemen Produk** — CRUD produk, upload gambar, set best seller / new arrival, stok minimum, varian
- **Manajemen Kategori** — CRUD kategori dengan slug otomatis
- **Manajemen User** — Lihat semua user, ubah role & status (aktif/nonaktif), hapus
- **Laporan** — Laporan penjualan per bulan, export CSV, export PDF
- **Pengaturan Toko** — Nama toko, email, nomor telepon, alamat, biaya COD, minimum free ongkir

---

## 📁 Struktur Direktori

```
lumier-parfum/
├── index.php               # Beranda
├── products.php            # Katalog produk
├── product-detail.php      # Detail produk
├── cart.php                # Keranjang belanja
├── checkout.php            # Proses checkout
├── order-success.php       # Halaman sukses order
├── tracking.php            # Lacak pesanan
├── login.php               # Halaman login
├── register.php            # Halaman registrasi
├── forgot-password.php     # Form lupa password
├── reset-password.php      # Form reset password
├── logout.php              # Proses logout
├── wishlist.php            # Redirect wishlist
├── faq.php                 # FAQ
├── about.php               # Tentang kami
├── contact.php             # Kontak
├── privacy.php             # Kebijakan privasi
├── terms.php               # Syarat & ketentuan
│
├── admin/                  # Panel Admin
│   ├── dashboard.php
│   ├── orders.php
│   ├── order-detail.php
│   ├── products.php
│   ├── categories.php
│   ├── users.php
│   ├── reports.php
│   ├── settings.php
│   ├── login.php
│   └── includes/
│       └── admin-sidebar.php
│
├── user/                   # Panel Customer
│   ├── dashboard.php
│   ├── orders.php
│   ├── order-detail.php
│   ├── profile.php
│   ├── change-password.php
│   ├── wishlist.php
│   └── payment.php
│
├── config/
│   └── database.php        # Konfigurasi koneksi DB
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── session.php
│   └── functions.php       # Helper functions
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── main.js
│   │   ├── cart.js
│   │   ├── checkout.js
│   │   └── export-pdf.js
│   └── images/
│       ├── products/
│       └── icons/
│
└── lumier_parfum.sql       # File dump database
```

---

## 💻 Persyaratan Sistem

- **PHP** >= 8.0
- **MySQL** >= 5.7 atau **MariaDB** >= 10.4
- **Apache** dengan `mod_rewrite` aktif
- **XAMPP** / **Laragon** / **WAMP** (untuk lokal)
- Browser modern (Chrome, Firefox, Edge, Safari)

---

## 🚀 Setup & Instalasi

### 1. Clone / Download Project

```bash
# Clone repository
git clone https://github.com/username/lumier-parfum.git

# Atau ekstrak ZIP ke folder htdocs
```

### 2. Pindahkan ke Web Root

Pindahkan folder project ke:

- **XAMPP** → `C:/xampp/htdocs/perfume/`
- **Laragon** → `C:/laragon/www/perfume/`

### 3. Buat Database

Buka **phpMyAdmin** (`http://localhost/phpmyadmin`) atau MySQL CLI:

```sql
CREATE DATABASE lumier_parfum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Import Database

```bash
# Via MySQL CLI
mysql -u root -p lumier_parfum < lumier_parfum.sql
```

Atau lewat phpMyAdmin:
1. Pilih database `lumier_parfum`
2. Klik tab **Import**
3. Pilih file `lumier_parfum.sql`
4. Klik **Go**

### 5. Konfigurasi Database

Buka `config/database.php` dan sesuaikan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // username MySQL Anda
define('DB_PASS', '');            // password MySQL Anda
define('DB_NAME', 'lumier_parfum');
```

### 6. Jalankan Aplikasi

Pastikan Apache & MySQL sudah berjalan, lalu buka browser:

```
http://localhost/perfume/
```

---

## 🔑 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lumier.com | `admin123` |
| Customer | customer@email.com | `password123` |

> **Tip:** Di halaman login tersedia tombol "Demo Login" untuk mengisi otomatis.

---

## 🗄️ Skema Database

Project menggunakan **19 tabel**:

| Tabel | Keterangan |
|-------|------------|
| `users` | Data pengguna (admin & customer) |
| `addresses` | Alamat pengiriman tersimpan per user |
| `products` | Data produk parfum |
| `product_variants` | Varian ukuran/konsentrasi per produk |
| `product_gallery` | Foto tambahan produk |
| `product_notes` | Catatan aroma (top/middle/base note) |
| `brands` | Merek parfum |
| `categories` | Kategori produk |
| `carts` | Item keranjang belanja (DB-based) |
| `orders` | Data pesanan |
| `order_items` | Item dalam pesanan |
| `order_status_logs` | Log perubahan status pesanan |
| `payments` | Data pembayaran |
| `reviews` | Ulasan & rating produk |
| `wishlists` | Daftar wishlist user |
| `promo_codes` | Kode promo & diskon |
| `sliders` | Banner slider homepage |
| `settings` | Pengaturan toko |
| `password_resets` | Token reset password (berlaku 1 jam) |

---

## 🔄 Alur Penggunaan

### Alur Belanja Customer

```
Beranda → Katalog Produk → Detail Produk
    → Tambah ke Keranjang → Checkout
    → Pilih Alamat + Kurir + Pembayaran
    → Konfirmasi Order → Order Success
    → Lacak Pesanan via Kode Order
```

### Alur Lupa Password

```
Login → Klik "Lupa Password?"
    → Masukkan Email → Salin Link Reset
    → Buka Link → Isi Password Baru
    → Login dengan Password Baru
```

### Alur Admin Kelola Pesanan

```
Dashboard Admin → Manajemen Pesanan
    → Lihat Detail Pesanan
    → Update Status: pending → diproses → dikirim (auto-resi) → selesai
```

---

## ⚠️ Catatan Pengembangan

- **Lupa Password**: Saat ini link reset ditampilkan langsung di halaman (mode demo). Untuk production, integrasikan dengan PHPMailer/SMTP agar dikirim via email.
- **Upload Gambar**: Pastikan folder `assets/images/products/` memiliki permission write.
- **Hapus file helper**: Setelah setup, hapus `generate_hash.php` dan `reset_password.php` dari server.
- **HTTPS**: Aktifkan SSL/HTTPS di production untuk keamanan data login.
