# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# Aplikasi Web Coffee Shop

> **Status**: Dokumen ini mencantumkan status implementasi. ✅ = sudah diimplementasikan di project, 🔜 = roadmap (belum dikerjakan). Referensi teknis: `.agents/task-instruction.md`; status progres: `.agents/task-list.md`.

---

## 1. Latar Belakang
Coffee shop membutuhkan sistem digital terintegrasi untuk mengelola penjualan online maupun offline, stok bahan baku, serta memberikan pengalaman pemesanan yang nyaman bagi pelanggan.

## 2. Tujuan
1. Memungkinkan pelanggan memesan produk secara online (pickup/delivery).
2. Mempermudah kasir melakukan transaksi cepat via POS.
3. Memberikan admin kontrol penuh atas produk, stok, promo, dan laporan penjualan.
4. Menyediakan data analitik untuk pengambilan keputusan bisnis.

## 3. Target Pengguna (User Roles)
| Role | Deskripsi | Status |
|---|---|---|
| **Super Admin** | Akses penuh seluruh sistem | ✅ |
| **Admin/Manager** | Kelola produk, kategori, promo, laporan, pegawai | ✅ |
| **Kasir (Cashier)** | Akses POS untuk transaksi offline | ✅ |
| **Barista/Staff Dapur** | Lihat & update status pesanan (kitchen display) | ✅ |
| **Customer** | Registrasi, pesan online, lihat riwayat, member/loyalty | ✅ |

## 4. Ruang Lingkup Fitur

### A. Modul Customer (Web/PWA)
- ✅ Registrasi & login (email)
- ✅ Verifikasi email & reset password
- ✅ Katalog produk dengan kategori (Coffee, Non-Coffee, Snack, dsb), filter & search
- ✅ Detail produk: gambar, deskripsi, varian (size, sugar level, milk type, topping/add-on), harga otomatis menyesuaikan varian
- ✅ Keranjang belanja (cart) — tambah, ubah qty, hapus
- ✅ Checkout: pilih metode (Dine-in / Take Away / Delivery), pilih outlet/cabang, catatan pesanan
- 🔜 Integrasi payment gateway (Midtrans/Xendit) — saat ini simulasi `PaymentService` ✅
- ✅ Tracking status pesanan real-time (Pending → Diproses → Siap → Selesai/Diantar, via polling)
- ✅ Riwayat pesanan & invoice/struk digital (browser print); 🔜 PDF
- ✅ Program loyalty/membership (poin reward, tukar poin dengan voucher)
- ✅ Voucher/kode promo & diskon
- ✅ Review & rating produk
- 🔜 Notifikasi (email/WhatsApp/push) status pesanan
- ✅ Wishlist/favorit produk
- 🔜 Multi-cabang/outlet (saat ini single outlet)
- 🔜 Reservasi meja (opsional, untuk dine-in)

### B. Modul Point of Sale (POS) — untuk Kasir
- ✅ Interface kasir cepat (touch-friendly), grid produk per kategori
- ✅ Input pesanan manual dine-in/take away
- ✅ Metode pembayaran gabungan (cash, QRIS, kartu, e-wallet); 🔜 split bill
- ✅ Cetak struk (browser print)
- ✅ Buka/tutup shift kasir dengan rekap kas (cash drawer reconciliation)
- 🔜 Void/refund transaksi (dengan approval admin)
- ✅ Riwayat transaksi harian per kasir

### C. Modul Admin/Manajemen
- ✅ Dashboard analitik: total penjualan, produk terlaris, grafik tren harian/bulanan
- ✅ CRUD Produk (nama, harga, kategori, gambar, varian, status aktif/nonaktif)
- ✅ CRUD Kategori produk
- ✅ CRUD Banner (gambar, tautan, urutan)
- ✅ Manajemen Bahan Baku & Stok (inventory), termasuk resep (recipe) per produk untuk auto-deduct stok
- ✅ Alert stok menipis (low stock notification)
- ✅ Manajemen Outlet/Cabang
- ✅ Manajemen Meja (untuk dine-in)
- ✅ Manajemen Karyawan & Role/Permission (admin, kasir, barista)
- ✅ Manajemen Promo/Voucher/Diskon (persentase, nominal, periode berlaku)
- ✅ Manajemen Pesanan (semua channel: online & POS), update status
- ✅ Laporan: penjualan, produk terlaris, laba rugi sederhana, laporan stok, laporan per kasir/shift
- 🔜 Export laporan (Excel/PDF)
- 🔜 Manajemen Member/Customer & poin loyalty (saat ini poin di sisi customer)
- ✅ Pengaturan umum (nama toko, jam operasional, pajak/service charge, metode pembayaran aktif)
- 🔜 Log aktivitas (audit trail)

### D. Modul Kitchen Display System
- ✅ Layar dapur menampilkan antrian pesanan real-time (polling)
- ✅ Tandai item selesai dibuat

## 5. Non-Functional Requirements
- **Keamanan** ✅: hashing password, CSRF protection, role-based access control (RBAC), validasi input ketat (Form Request + Policy).
- **Performa** 🔜: caching (Redis) belum; ✅ lazy loading/pagination & eager loading diterapkan.
- **Skalabilitas** ✅: queue (`queue:listen`) untuk notifikasi/efek samping; 🔜 multi-outlet penuh.
- **Responsif** ✅: mobile-first design; 🔜 PWA-ready.
- **Real-time** ✅: update status pesanan & kitchen display via Livewire polling.
- **Testing** ✅: unit test & feature test untuk modul kritikal (checkout, pembayaran, stok).
- **Localization** ✅: Bahasa Indonesia sebagai default, format mata uang Rupiah.

## 6. Acceptance Criteria (Contoh)
- ✅ Pelanggan dapat menyelesaikan alur pemesanan dari pilih produk hingga pembayaran sukses tanpa error.
- ✅ Stok bahan baku otomatis berkurang sesuai resep saat pesanan dikonfirmasi.
- ✅ Kasir dapat menyelesaikan transaksi POS dengan cepat untuk pesanan sederhana.
- ✅ Admin dapat melihat laporan penjualan harian secara akurat dan real-time.
