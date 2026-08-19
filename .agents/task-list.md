# TASK-LIST — Implementasi Aplikasi Coffee Shop (Laravel 12)

Dokumen ini adalah rencana kerja eksekusi `.agents/task-instruction.md` (tech spec).
Keputusan scope yang sudah disetujui:

- **Scope**: Core-first (data, auth/RBAC, admin, POS, customer menu/cart/checkout, kitchen display). Laporan/loyalty/promo/review termasuk tapi lean.
- **Payment**: Gateway **simulasi/mock** (`PaymentService`), siap diganti Midtrans/Xendit di belakang interface yang sama.
- **Realtime**: **Livewire polling** (`wire:poll`), tanpa infra tambahan.
- **Auth**: **Breeze (Blade)**.
- **Stack**: Blade + Livewire 4 + Alpine + Tailwind 4, PHPUnit, Spatie laravel-permission, Intervention Image.
- **DB lokal**: **MySQL 8** (Laragon `127.0.0.1:3306`, root tanpa password) — dev `coffeeshop`, test `coffeeshop_test` (phpunit.xml). UI Bahasa Indonesia + format Rupiah.

Konvensi wajib (dari tech spec + AGENTS.md):
- Validasi via **Form Request** (bukan inline di controller).
- Otorisasi via **Policy** per resource.
- Logika bisnis kompleks di **Service class** (Stock, Payment, Loyalty, Report, Order, ProductImage).
- Efek samping via **Event + Listener** (`OrderCompleted` → notifikasi + tambah poin + kurangi stok; `OrderCancelled` → restore stok + balikin poin).
- Struktur: `app/Http/Controllers/{Customer,Pos,Admin,Kitchen}/`, `app/Services/`, `app/Events/`, `app/Listeners/`, `app/Policies/`.
- **Livewire 4 single-file components** di `resources/views/components/{pos,customer,kitchen}/` (tanpa `app/Livewire/`).
- Testing minimal: checkout, pembayaran, pengurangan stok.

---

## [x] Fase 0 — Setup & Fondasi
- [x] `composer require spatie/laravel-permission livewire/livewire` dan `laravel/breeze` (dev), lalu `php artisan breeze:install blade`
- [x] Publish config: `spatie/permission`, `livewire`; verifikasi Tailwind 4 + Vite tetap jalan setelah Breeze
- [x] `resources/css/app.css`: extend `@theme` dengan token desain Roasted & Refined (palet warna, Playfair Display + Inter via Google Fonts, radius 12px, soft shadow)
- [x] Layout Blade utama + komponen bersama (button, card, badge, navbar) memakai `@vite`
- [x] `config/app.php`: `locale=id`, `faker_locale=id_ID`, timezone `Asia/Jakarta`; helper `App\Support\Rupiah` + directive `@rupiah`

> **Catatan**: Breeze `blade` menginstall Tailwind 3 (tailwind.config.js + postcss.config.js). Sudah di-rollback ke Tailwind 4 (`@theme` di app.css), file config TS dihapus.
> **Gotcha environment**: `php.ini` CLI (Laragon `php-8.3.33`) punya `pdo_sqlite`/`sqlite3` nonaktif → test gagal "could not find driver". Sudah diaktifkan.
> **DB switch**: Basis data sudah diganti ke **MySQL 8** (Laragon `127.0.0.1:3306`, root tanpa password). Dev = `coffeeshop`, test = `coffeeshop_test` (phpunit.xml). File `database/database.sqlite` dihapus.

## [x] Fase 1 — Data Layer (Migration + Model)
- [x] Migrasi: `outlets`
- [x] Migrasi: tambah `outlet_id` + `phone` ke `users`
- [x] Migrasi: `categories`, `products`
- [x] Migrasi: `product_variants` (tipe: size/sugar/milk/topping, harga_tambahan)
- [x] Migrasi: `ingredients`, `recipes`
- [x] Migrasi: `promos`
- [x] Migrasi: `orders` (kode_order, tipe dine-in/takeaway/delivery, status, total, metode_bayar, pajak/service), `order_items` (variant JSON, qty, harga)
- [x] Migrasi: `payments`, `loyalty_points`, `shifts`, `tables`, `reviews`
- [x] Migrasi: `settings` (pengaturan umum), `banners` (CRUD banner)
- [x] Model + relationship + factory untuk seluruh tabel

## [x] Fase 2 — Auth + RBAC
- [x] Breeze blade auth (login/register/verifikasi/reset)
- [x] Seeder role: super-admin, admin, kasir, barista, customer
- [x] Listener: user registrasi baru → role `customer`
- [x] Alias middleware `role`/`permission` Spatie di `bootstrap/app.php`
- [x] Route group `{Customer,Pos,Admin,Kitchen}` di `routes/web.php`

## [x] Fase 3 — Seeder Data Dummy
- [x] 1 outlet; 1 user tiap role staff + demo customer
- [x] Kategori (3), produk (~10) dengan varian, bahan baku + resep (untuk auto-deduct)
- [x] Promo, meja, shift, beberapa order contoh

## [x] Fase 4 — Modul Admin
- [x] Dashboard: KPI (total penjualan, produk terlaris, tren harian)
- [x] CRUD Kategori
- [x] CRUD Banner (gambar via `ProductImageService` + `intervention/image`)
- [x] CRUD Produk (upload gambar)
- [x] CRUD Varian
- [x] CRUD Bahan Baku (ingredient)
- [x] CRUD Resep
- [x] CRUD Promo
- [x] CRUD Outlet, CRUD Meja
- [x] Manajemen Karyawan + assignment role
- [x] Manajemen Pesanan (update status)
- [x] Pengaturan umum (nama toko, jam operasional, pajak/service, metode bayar)
- [x] Alert stok menipis (low stock)
- [x] Form Request + Policy untuk semua resource admin

## [x] Fase 5 — Modul POS (Kasir)
- [x] Livewire `PosInterface` (mengikuti design system Roasted & Refined)
- [x] Grid produk + tab kategori + search + sidebar order + qty
- [x] Metode pembayaran (cash, QRIS, kartu, e-wallet)
- [x] Charge → `OrderService` buat order + `PaymentService` (simulasi) + `StockService` kurangi stok
- [x] Cetak struk (browser print)
- [x] Shift buka/tutup + rekonsiliasi kas
- [x] Riwayat transaksi per kasir

## [x] Fase 6 — Modul Customer
- [x] Katalog Livewire (search/filter/kategori) — mengikuti design system Roasted & Refined
- [x] Detail produk + pilihan varian (size/sugar/milk/topping) + harga otomatis
- [x] Cart (session) + floating cart
- [x] Checkout: pilih outlet, tipe (dine-in/takeaway/delivery), promo/voucher, catatan
- [x] Order tracking status (polling)
- [x] Riwayat pesanan + invoice (struk)
- [x] Loyalty: akumulasi poin saat order selesai + tukar voucher
- [x] Review & rating produk
- [x] Wishlist/favorit

## [x] Fase 7 — Kitchen Display (Barista)
- [x] Livewire `KitchenDisplay`: antrian pesanan, update status item (Pending → Diproses → Siap → Selesai)
- [x] Update status order real-time via polling

## [x] Fase 8 — Laporan (Admin)
- [x] Penjualan harian/bulanan
- [x] Produk terlaris
- [x] Per kasir/shift
- [x] Laporan stok
- [x] Laba rugi sederhana
- [ ] (Opsional) Export Excel (maatwebsite/excel) / PDF (dompdf) — **belum dikerjakan**, pindah ke roadmap

## [x] Fase 9 — Testing & Penutupan
- [x] Feature test: checkout (wajib) — `FullFlowTest`, `CustomerFlowTest`
- [x] Feature test: pembayaran (wajib) — `PosOrdersTest`
- [x] Feature test: pengurangan stok (wajib) — `FullFlowTest`, `PosOrdersTest`
- [x] Feature test: auth + RBAC — `RegistrationRbacTest`, `Auth/*`
- [x] Smoke test CRUD kunci — `AdminSmokeTest`, `KitchenDisplayTest`, `PosSmokeTest`
- [x] Test tambahan: `CustomerOrderStatusTest`, `AdminProductImageTest`, `AdminBannerTest`, `ProfileTest`
- [x] `vendor/bin/pint`
- [x] `composer test` (semua hijau)
- [x] Update `README.md` (dokumentasi instalasi) + `AGENTS.md`

---

## Roadmap lanjutan (di luar scope core — untuk iterasi berikutnya)
- [ ] Integrasi payment gateway nyata (Midtrans/Xendit)
- [ ] Realtime Reverb/Pusher (ganti polling)
- [ ] Export laporan Excel (maatwebsite/excel) / PDF (dompdf)
- [ ] Reservasi meja + QR code per meja
- [ ] Notifikasi WhatsApp/email (queue)
- [ ] PWA / mobile-first polish
- [ ] Multi-cabang penuh (produk/harga per outlet)
- [ ] Audit trail / log aktivitas
- [ ] Kitchen display lanjutan + antrian per item selesai