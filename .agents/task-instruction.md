# INSTRUKSI TEKNIS (TECH SPEC)
# Aplikasi Web Coffee Shop — Laravel 12

Dokumen ini adalah acuan teknis pengembangan. Gunakan bersama `.agents/prd.md` (requirement fitur) dan `.agents/task-list.md` (status eksekusi).

---

## 1. Tech Stack (implementasi nyata)
- **Backend**: Laravel 12 (PHP 8.3+, Laragon)
- **Frontend**: Blade + **Livewire 4** (single-file components) + Alpine.js + **Tailwind CSS 4** (`@theme` di `resources/css/app.css`, tanpa tailwind.config.js)
- **Database**: **MySQL 8** (Laragon `127.0.0.1:3306`, root tanpa password). Dev = `coffeeshop`, test = `coffeeshop_test` (phpunit.xml).
- **Auth**: Laravel Breeze (Blade) + Spatie `laravel-permission` (RBAC)
- **Payment**: `App\Services\PaymentService` — **simulasi/mock**, interface siap diganti Midtrans/Xendit
- **Realtime**: **Livewire polling** (`wire:poll.4s` / `wire:poll.5s`) — tanpa Reverb/Pusher
- **Queue**: `queue:listen` (bawaan Laravel) — tanpa Redis/Horizon
- **Storage**: Local `storage/app/public` (upload gambar produk/banner via `ProductImageService` + `intervention/image`)
- **Testing**: **PHPUnit** (`composer test`)

## 2. Struktur Modul/Domain (implementasi nyata)
```
app/
├── Models/ (User, Product, Category, ProductVariant, Order, OrderItem, Ingredient,
│            Recipe, Outlet, Table, Promo, Payment, LoyaltyPoint, Shift, Review,
│            Setting, Banner)
├── Http/Controllers/
│   ├── Customer/ (HomeController, OrderController)
│   ├── Pos/ (PosController)
│   ├── Admin/ (Dashboard, Category, Product, Variant, Ingredient, Recipe, Promo,
│   │           Outlet, Table, Employee, Order, Setting, Report, Banner)
│   └── Kitchen/ (KitchenController)
├── Http/Requests/ (Form Request per aksi — validasi TIDAK inline di controller)
├── Policies/ (Policy per resource admin)
├── Services/ (OrderService, PaymentService, StockService, LoyaltyService,
│              ReportService, ProductImageService)
├── Events/ (OrderCompleted, OrderCancelled)
└── Listeners/ (DeductStock, AddLoyaltyPoints, SendOrderNotification,
                RestoreStock, ReverseLoyaltyPoints, AssignCustomerRole)
```

**Livewire 4 single-file components** (tanpa `app/Livewire/`):
```
resources/views/components/pos/      → ⚡pos-interface.blade.php, ⚡orders.blade.php
resources/views/components/customer/ → ⚡catalog, ⚡checkout, ⚡order-status, ⚡loyalty, ⚡review-form
resources/views/components/kitchen/  → ⚡display.blade.php
```

## 3. Skema Database (implementasi nyata)
- `users` (+ `outlet_id`, `phone`)
- `outlets` (nama, alamat, jam operasional)
- `categories`
- `products` (category_id, nama, deskripsi, harga_dasar, gambar, status)
- `product_variants` (product_id, tipe [size/sugar/milk/topping], nama, harga_tambahan)
- `ingredients` (nama, satuan, stok_saat_ini, stok_minimum)
- `recipes` (product_id, ingredient_id, jumlah_terpakai)
- `orders` (user_id, outlet_id, kode_order, tipe [dine-in/takeaway/delivery], status, total, pajak/service, metode_bayar, promo_id, shift_id, kasir_id)
- `order_items` (order_id, product_id, variant JSON, qty, harga, catatan)
- `payments` (order_id, metode, status, jumlah)
- `promos` (kode, tipe_diskon, nilai, periode)
- `loyalty_points` (user_id, poin, keterangan)
- `shifts` (kasir_id, outlet_id, kas_awal, kas_akhir, waktu_buka, waktu_tutup, status)
- `tables` (outlet_id, nomor_meja, status)
- `reviews` (user_id, product_id, order_id, rating, komentar)
- `settings` (key, value — pengaturan umum toko)
- `banners` (judul, deskripsi, gambar, tautan, urutan, is_active)

## 4. Konvensi Wajib
- **Form Request** untuk validasi (bukan inline di controller).
- **Policy** untuk otorisasi per resource.
- **Service class** untuk logika bisnis kompleks (stok, poin loyalty, payment).
- **Event + Listener** untuk efek samping:
  - `OrderCompleted` → `DeductStock`, `AddLoyaltyPoints`, `SendOrderNotification`
  - `OrderCancelled` → `RestoreStock`, `ReverseLoyaltyPoints`
  - Registrasi user → `AssignCustomerRole` (role `customer`)
- **RBAC** via Spatie; roles: `super-admin`, `admin`, `kasir`, `barista`, `customer`.
- Alias middleware `role`/`permission`/`role_or_permission` di `bootstrap/app.php`.
- **Livewire**: komponen di `resources/views/components/{pos,customer,kitchen}/`; property computed diakses `$this->prop`; action redirect tidak bertipe `void`.
- **Rupiah**: `App\Support\Rupiah` + directive `@rupiah`; UI Bahasa Indonesia (`config/app.php`: locale `id`, timezone `Asia/Jakarta`).

## 5. Testing
- Minimal wajib: checkout, pembayaran, pengurangan stok.
- Implementasi: `tests/Feature/{PosSmokeTest,PosOrdersTest,CustomerFlowTest,CustomerOrderStatusTest,FullFlowTest,KitchenDisplayTest,AdminSmokeTest,AdminProductImageTest,AdminBannerTest,RegistrationRbacTest,ProfileTest}` + `tests/Feature/Auth/*`.
- Jalankan `composer test` (config:clear + artisan test) atau `php artisan test --filter=NamaTest`.
- Format kode: `vendor/bin/pint`.

## 6. Perintah Kerja
- `composer dev` — serve + queue:listen + pail + Vite (day-to-day).
- `composer setup` — bootstrap ulang (install, .env, key, migrate, npm build).
- `npm.cmd run build` / `npm.cmd run dev` (PowerShell tidak bisa memanggil `npm` langsung).

## 7. Roadmap (di luar scope saat ini)
- Integrasi payment gateway nyata (Midtrans/Xendit).
- Realtime Reverb/Pusher menggantikan polling.
- Export laporan Excel (maatwebsite/excel) / PDF (dompdf).
- Reservasi meja + QR code, notifikasi WhatsApp/email, PWA, multi-cabang penuh, audit trail.
