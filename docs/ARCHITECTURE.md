**Project Name:** Modern POS Rewrite (dari Microsoft Access)  
**Version:** 1.0 (MVP)  
**Last Updated:** 2026-04-09  
**Purpose:** Dokumen arsitektur resmi sebagai main reference untuk AI & developer saat implementasi.

## 1. Project Overview
Aplikasi Point of Sales (POS) modern untuk retail kecil-menengah.  
Fokus utama:
- Master Stok & Inventory
- Transaksi Penjualan cepat
- Shift Cash Reconciliation + Blind Count (cegah discrepancy modal)
- Payment Gateway (Midtrans/Xendit + QRIS)
- Immutable Audit Log + Exception Alerts

Total 9 fitur MVP (lihat GitHub Issues di Milestone **MVP v1.0**).

## 2. Tech Stack
| Layer          | Technology                          | Version (recommended) |
|----------------|-------------------------------------|-----------------------|
| Backend        | Laravel (PHP)                       | 13.x                  |
| Database       | PostgreSQL                          | 16.x                  |
| Frontend       | Blade + Tailwind CSS                | Laravel default + Tailwind 3.x |
| Authentication | Laravel Sanctum / Built-in Auth     | -                     |
| Queue          | Laravel Queue (database driver)     | -                     |
| Deployment     | Railway / Forge / VPS               | -                     |

**Tidak digunakan:** Inertia, Livewire, Vue/React, Redis (kecuali scaling nanti).

## 3. High-Level Architecture (Layered)
Kita pakai **Layered Architecture** klasik Laravel + Domain-Driven Design ringan:

```
Presentation Layer (Blade + Tailwind)
         ↓
Application Layer (Controllers + Request + Services)
         ↓
Domain Layer (Models + Policies + Events + Jobs)
         ↓
Infrastructure Layer (Eloquent + Repositories + Migrations + Providers)
```

- **Controllers** → tipis, hanya routing & validation.
- **Services** → business logic berat (ShiftService, PaymentService, ReconciliationService).
- **Models** → Eloquent dengan scopes, accessors, mutators, dan event listeners.
- **Events & Listeners** → untuk audit log otomatis & notification.

## 4. Database Schema (3NF - Production Ready)
Lihat file `database/migrations/` untuk skema lengkap.  
Total **9 tabel** (semua dipakai di MVP):

- `categories`, `units`
- `products` (stok real-time)
- `suppliers`
- `users` (merge pegawai + pengguna, role: admin / cashier)
- `stock_in`
- `sales` (link ke shift & payment)
- `shifts` (baru - reconciliation)
- `cash_flow`
- `audit_log` (immutable - JSONB old/new data)

**Important Rules:**
- Semua tabel pakai `created_at` & `updated_at` (timestamps).
- `stok` di `products` di-update via **database trigger** + Eloquent events.
- `variance` di `shifts` pakai **GENERATED ALWAYS AS** column.
- Audit log di-trigger otomatis via Laravel Event/Listener.

## 5. Folder Structure (Laravel Standard + Custom)
```txt
app/
├── Domain/              # Business logic
│   ├── Models/          # Eloquent Models
│   ├── Services/        # ShiftService, PaymentService, etc.
│   ├── Events/
│   ├── Listeners/
│   └── Repositories/    # Optional, kalau logic kompleks
├── Http/
│   ├── Controllers/
│   ├── Requests/        # Form Requests
│   └── Middleware/
resources/
├── views/               # Blade templates (layouts + components)
├── css/                 # Tailwind
database/
├── migrations/
├── seeders/             # Dummy data (20 products, 5 shifts)
routes/
├── web.php
├── api.php              # untuk webhook payment
```

## 6. Key Design Patterns & Conventions
- **Repository Pattern** (opsional, tapi recommended untuk Services).
- **Service Pattern** untuk semua business logic kompleks.
- **Event-Driven** untuk audit log & alerts.
- **Immutable Transactions** → `sales` & `shifts` tidak boleh di-update setelah closed.
- **Blade Components** untuk reusable UI (pos-screen, shift-count-form, etc.).
- **Tailwind** pakai utility-first + custom components di `resources/views/components/`.

## 7. Data Flow Utama (Contoh)
### POS Transaction Flow
1. Cashier login → buka shift (Issue 5)
2. POS screen (Blade) → cari product → tambah keranjang
3. Checkout → pilih payment (cash / gateway) → Issue 6
4. Simpan ke `sales` → trigger update `products.stok` + `cash_flow`
5. Event `SaleCreated` → buat audit_log + notifikasi kalau anomaly
6. Akhir shift → blind count → hitung variance → alert kalau ≠ 0

### Reconciliation Flow
- Start shift → simpan `starting_float`
- End shift → cashier input `ending_cash` (blind)
- System hitung expected cash dari `sales` + `cash_flow`
- Simpan variance + status closed

## 8. Security & Audit
- Password → `Hash::make()` (bcrypt)
- Role-based via Laravel Gates & Policies
- All mutations lewat `audit_log` (immutable)
- Payment webhook → verify signature
- Rate limiting di login & API
- Input sanitization via Form Requests

## 9. GitHub Issues Reference
Semua pekerjaan di-track via 9 issues di Milestone **MVP v1.0**:
- Issue 1 → Database Setup
- Issue 2 → Master Stok
- Issue 3 → Auth
- Issue 4 → Sales Transaction
- Issue 5 → Shift Reconciliation
- Issue 6 → Payment Gateway
- Issue 7 → Reporting + Audit Log
- Issue 8 → Deployment
- Issue 9 → QA & Security

## 10. Deployment & Environment
- `.env` example ada di repo
- Migration otomatis via `php artisan migrate --force`
- Queue worker untuk notifikasi & webhook
- Storage: public disk untuk struk PDF (opsional)
