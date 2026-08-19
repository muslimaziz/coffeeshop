# Bean & Brew — Coffee Shop (Laravel 12)

Aplikasi web manajemen coffee shop lengkap: katalog menu, POS kasir, kitchen display, admin, loyalty, dan review. UI Bahasa Indonesia, desain **Roasted & Refined** (Playfair Display + Inter).

## Fitur

- **Customer**: katalog menu (search/filter/kategori), detail produk + varian (size/sugar/milk/topping), keranjang, checkout (pilih outlet, tipe pesanan, promo), order tracking, riwayat + struk, loyalty points, review & rating, wishlist.
- **POS Kasir**: grid produk + tab kategori + search, sidebar order, metode bayar (cash/QRIS/kartu/e-wallet), promo, shift buka/tutup + rekonsiliasi kas, struk cetak, riwayat transaksi.
- **Kitchen Display**: antrian pesanan real-time (polling), update status Pending → Diproses → Siap → Selesai.
- **Admin**: dashboard KPI, CRUD kategori/produk/varian/bahan/resep/promo/outlet/meja/karyawan, manajemen pesanan, pengaturan umum (pajak/service), laporan penjualan (harian, produk terlaris, per kasir, laba rugi) + laporan stok.
- **RBAC**: super-admin, admin, kasir, barista, customer (Spatie laravel-permission).
- **Payment**: gateway simulasi/mock (`PaymentService`), siap diganti Midtrans/Xendit di balik interface yang sama.

## Tech Stack

- Laravel 12, PHP 8.3, MySQL 8
- Blade + Livewire 4 + Alpine + Tailwind CSS 4 (Vite)
- Spatie laravel-permission, Laravel Breeze (Blade), PHPUnit

## Requirements

- PHP 8.3 (CLI `pdo_sqlite`/`sqlite3` tidak dibutuhkan — memakai MySQL)
- MySQL 8 (Laragon: `127.0.0.1:3306`, root tanpa password)
- Node.js 20+ / npm

## Instalasi

```bash
# 1. Install dependensi + setup (composer install, .env, key:generate, migrate, npm)
composer setup

# 2. Buat symlink storage agar gambar produk/banner tampil
php artisan storage:link

# 3. Jalankan development (artisan serve + queue + Vite sekaligus)
composer dev
```

Buka `http://localhost:8000`. Login pakai akun seeder (password semua `password`):

| Email | Role |
|---|---|
| `superadmin@beanbrew.test` | super-admin |
| `admin@beanbrew.test` | admin |
| `kasir@beanbrew.test` | kasir |
| `barista@beanbrew.test` | barista |
| `customer@example.com` | customer |

Seeder data: 1 outlet, 10 produk + 76 varian + 25 resep + 10 bahan, 2 promo (`HEMATHARI`, `NGOPI5K`), 8 meja, contoh order/shift/loyalty/review.

## Test

```bash
composer test              # semua test
php artisan test --filter=PosSmokeTest
```

Konteks: test berjalan di database MySQL `coffeeshop_test` (lihat `phpunit.xml`).

## Lint

```bash
vendor/bin/pint
```

## Struktur Modul

```
app/Http/Controllers/{Customer,Pos,Admin,Kitchen}/
app/Services/                        # Order, Stock, Payment, Loyalty, Report
app/Events/ + app/Listeners/         # efek samping (OrderCompleted → poin, stok, notifikasi)
app/Http/Requests/Admin/             # validasi (Form Request)
app/Policies/                        # otorisasi per resource
database/seeders/                    # Role, Outlet, User, Catalog, DemoData
resources/views/components/pos, customer, kitchen/   # komponen Livewire single-file
```

## Dokumentasi

- `.agents/prd.md` — product spec (Bahasa Indonesia, status implementasi)
- `.agents/task-instruction.md` — tech spec & konvensi
- `.agents/task-list.md` — status fase implementasi
- `.agents/design/roasted_refined/DESIGN.md` — design system (token warna, tipografi)

## Lisensi

MIT