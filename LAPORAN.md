# LAPORAN PROYEK PENGEMBANGAN APLIKASI WEB
## Lumière Parfum — Toko Parfum Premium Berbasis Web

**Nama Proyek** : Lumière Parfum  
**Teknologi**   : PHP Native, MySQL, HTML/CSS/JS  
**Repository**  : https://github.com/bastiandev9-dotcom/Parfum-website  

---

# BAB I: PENDAHULUAN

## 1.1 Latar Belakang & Tujuan

### Latar Belakang

Perkembangan teknologi internet yang pesat telah mengubah perilaku konsumen dalam berbelanja. Masyarakat kini semakin terbiasa melakukan transaksi secara daring karena kemudahan akses, efisiensi waktu, dan pilihan produk yang lebih beragam. Industri parfum sendiri merupakan segmen yang terus tumbuh, dengan konsumen yang semakin melek terhadap kualitas aroma dan brand premium.

Berangkat dari kondisi tersebut, dibangunlah **Lumière Parfum** — sebuah platform toko parfum premium berbasis web yang dirancang untuk memberikan pengalaman belanja yang elegan, mudah, dan aman. Aplikasi ini dibangun menggunakan PHP native tanpa framework agar ringan, mudah dipelajari, dan mudah di-deploy di lingkungan shared hosting maupun lokal (XAMPP/Laragon).

### Tujuan

1. Menyediakan platform belanja parfum online yang lengkap dan mudah digunakan.
2. Membantu pengelola toko dalam mengelola produk, pesanan, dan laporan penjualan secara terpusat melalui panel admin.
3. Memberikan pengalaman belanja yang aman dengan sistem autentikasi berbasis bcrypt dan proteksi SQL Injection menggunakan Prepared Statement.
4. Mengimplementasikan alur belanja yang lengkap mulai dari katalog produk, keranjang, checkout, hingga pelacakan pesanan.
5. Menjadi referensi proyek PHP native yang menerapkan praktik pengembangan web yang baik.

---

## 1.2 Ruang Lingkup

Berikut daftar fitur utama yang tersedia dalam aplikasi:

### Fitur untuk Pengunjung / Customer

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Beranda | Banner hero, produk best seller, produk unggulan |
| 2 | Katalog Produk | Filter kategori, brand, gender, harga; sorting; pencarian |
| 3 | Detail Produk | Galeri foto, varian ukuran, review & rating |
| 4 | Keranjang Belanja | Tambah/hapus/update qty, total otomatis |
| 5 | Wishlist | Simpan produk favorit |
| 6 | Checkout | Pilih alamat, kurir (JNE/J&T/SiCepat/AnterAja), promo, pembayaran (BCA/BNI/COD) |
| 7 | Lacak Pesanan | Cek status via kode order tanpa login |
| 8 | Halaman Statis | FAQ, Tentang Kami, Kebijakan Privasi, Syarat & Ketentuan, Kontak |

### Fitur Panel Customer (Login)

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Ringkasan pesanan aktif dan riwayat belanja |
| 2 | Riwayat Pesanan | Daftar semua pesanan beserta status |
| 3 | Detail Pesanan | Timeline status, detail item, info resi pengiriman |
| 4 | Profil | Edit nama, email, nomor telepon |
| 5 | Ganti Password | Verifikasi password lama sebelum ubah |
| 6 | Alamat | Kelola beberapa alamat pengiriman |
| 7 | Wishlist | Kelola daftar produk favorit |

### Fitur Autentikasi

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Registrasi | Daftar akun baru, validasi email unik |
| 2 | Login | Autentikasi dengan bcrypt |
| 3 | Lupa Password | Generate token (1 jam), tampilkan link reset |
| 4 | Reset Password | Ganti password via token, token hangus setelah dipakai |
| 5 | Logout | Destroy session |

### Fitur Panel Admin

| No | Fitur | Keterangan |
|----|-------|------------|
| 1 | Dashboard | Statistik penjualan, grafik bulanan, produk terlaris |
| 2 | Manajemen Pesanan | Update status, auto-generate nomor resi |
| 3 | Manajemen Produk | CRUD produk, upload gambar, varian |
| 4 | Manajemen Kategori | CRUD kategori dengan slug otomatis |
| 5 | Manajemen User | Ubah role & status user, hapus user |
| 6 | Laporan | Laporan penjualan per bulan, export CSV & PDF |
| 7 | Pengaturan Toko | Nama toko, email, biaya COD, minimal free ongkir |

---

# BAB II: ANALISIS & DESAIN

## 2.1 Kebutuhan Teknologi

### Stack Teknologi

| Layer | Teknologi | Keterangan |
|-------|-----------|------------|
| Backend | PHP 8.x (native) | Tanpa framework, menggunakan MySQLi |
| Database | MySQL 5.7+ / MariaDB 10.4+ | Relasional, charset utf8mb4 |
| Frontend | HTML5, CSS3, JavaScript (Vanilla) | Tanpa framework JS |
| Icon | Font Awesome 6.4 | CDN |
| Web Server | Apache | mod_rewrite aktif |
| Dev Environment | XAMPP / Laragon | Untuk pengembangan lokal |

### Kebutuhan Server

- PHP >= 8.0 dengan ekstensi `mysqli`, `session`, `openssl`
- MySQL >= 5.7 atau MariaDB >= 10.4
- Apache dengan `mod_rewrite` aktif
- Folder `assets/images/products/` harus memiliki permission write

---

## 2.2 Perancangan

### Diagram Relasi Antar Tabel (ERD — Tekstual)

```
users (user_id PK)
  ├── addresses       (user_id FK)
  ├── carts           (user_id FK)
  ├── orders          (user_id FK)
  ├── reviews         (user_id FK)
  ├── wishlists       (user_id FK)
  └── password_resets (email)

products (product_id PK)
  ├── product_variants  (product_id FK)
  ├── product_gallery   (product_id FK)
  ├── product_notes     (product_id FK)
  ├── order_items       (product_id FK)
  ├── reviews           (product_id FK)
  └── wishlists         (product_id FK)

brands     (brand_id PK)     → products (brand_id FK)
categories (category_id PK)  → products (category_id FK)

orders (order_id PK)
  ├── order_items        (order_id FK)
  ├── order_status_logs  (order_id FK)
  └── payments           (order_id FK)

promo_codes  (promo_id PK)   → digunakan saat checkout
sliders      (slider_id PK)  → banner homepage
settings     (s_key PK)      → konfigurasi toko
```

**Total: 19 tabel**

### Daftar Lengkap Tabel

| Tabel | Keterangan |
|-------|------------|
| `users` | Data pengguna (admin & customer) |
| `addresses` | Alamat pengiriman per user |
| `products` | Data produk parfum |
| `product_variants` | Varian ukuran/konsentrasi |
| `product_gallery` | Foto tambahan produk |
| `product_notes` | Catatan aroma (top/middle/base note) |
| `brands` | Merek parfum |
| `categories` | Kategori produk dengan slug |
| `carts` | Item keranjang belanja |
| `orders` | Data pesanan |
| `order_items` | Item dalam pesanan |
| `order_status_logs` | Log perubahan status pesanan |
| `payments` | Data pembayaran |
| `reviews` | Ulasan & rating produk |
| `wishlists` | Wishlist per user |
| `promo_codes` | Kode promo & diskon |
| `sliders` | Banner slider homepage |
| `settings` | Pengaturan toko |
| `password_resets` | Token reset password (berlaku 1 jam) |

### Alur Aplikasi (Wireframe Tekstual)

```
[Beranda]
  └─ [Katalog Produk] ──► [Detail Produk]
        └─ [Tambah ke Keranjang]
              └─ [Keranjang]
                    └─ [Checkout]
                          └─ [Order Success] ──► [Lacak Pesanan]

[Login / Register]
  └─ [Dashboard Customer]
        ├─ Riwayat Pesanan
        ├─ Profil & Password
        ├─ Alamat
        └─ Wishlist

[Admin Login]
  └─ [Dashboard Admin]
        ├─ Pesanan
        ├─ Produk & Kategori
        ├─ User
        ├─ Laporan
        └─ Pengaturan
```

