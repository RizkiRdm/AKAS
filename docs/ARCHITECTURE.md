# ARCHITECTURE.md

**Project Name:** AKAS — Modern POS (Rewrite dari Microsoft Access)
**Version:** 1.0 (MVP)
**Last Updated:** 2026-04-15
**Purpose:** Dokumen arsitektur resmi. Wajib dibaca oleh AI agent sebelum mengerjakan task apapun.
**Audience:** AI agent, solo developer

---

> ⚠️ CRITICAL RULES FOR AI AGENT
> 1. Jangan tambahkan dependency baru tanpa explicit approval di issue.
> 2. Jangan gunakan Livewire, Inertia, Alpine.js, Vue, React dalam bentuk apapun.
> 3. State management UI → gunakan vanilla JavaScript native (lihat Section 6).
> 4. Kalau ada ambiguitas → STOP dan minta klarifikasi, jangan assume.
> 5. Setiap Blade view wajib reference DESIGN.md di comment pertama.

---

## 1. Project Overview

Aplikasi Point of Sales modern untuk retail kecil-menengah. Dibangun ulang dari Microsoft Access.

**Fokus MVP:**
- Master Data (Produk, Kategori, Satuan, Supplier)
- Transaksi Penjualan real-time
- Shift Cash Reconciliation + Blind Count
- Payment Gateway (Midtrans/Xendit + QRIS)
- Immutable Audit Log + Exception Alerts

**Scope MVP:** 9 issue di Milestone MVP v1.0 (GitHub Issues).

---

## 2. Tech Stack

| Layer          | Technology                        | Version         |
|----------------|-----------------------------------|-----------------|
| Backend        | Laravel (PHP)                     | 12.x            |
| Database       | PostgreSQL                        | 16.x            |
| Frontend       | Blade + Tailwind CSS              | Tailwind 3.x    |
| Authentication | Laravel Sanctum / Built-in Auth   | —               |
| Queue          | Laravel Queue (database driver)   | —               |
| Deployment     | Railway / Forge / VPS             | —               |

**EXPLICITLY NOT USED (jangan tambahkan):**
- ❌ Inertia.js
- ❌ Livewire
- ❌ Vue / React / Svelte
- ❌ Alpine.js
- ❌ Redis (kecuali ada approval eksplisit untuk scaling)
- ❌ Any JavaScript framework atau reactive library

---

## 3. Layered Architecture

```
Presentation Layer     → Blade templates + Tailwind CSS + Vanilla JS
         ↓
Application Layer      → Controllers + Form Requests + Services
         ↓
Domain Layer           → Models + Policies + Events + Jobs
         ↓
Infrastructure Layer   → Eloquent + Repositories + Migrations + Providers
```

**Rules per layer:**

### Controllers
- Tipis: hanya handle routing, validation delegation, dan response.
- Tidak boleh ada business logic di controller.
- Gunakan Form Request untuk validasi.
- Gunakan Service untuk logic.

### Services
- Semua business logic berat ada di sini.
- Naming: `[Domain]Service.php` (contoh: `ShiftService`, `PaymentService`, `StockService`).
- Services tidak boleh inject Controller.
- Services boleh inject Repository atau Model langsung.

### Models
- Gunakan Eloquent scopes, accessors, mutators.
- Event listeners untuk audit log otomatis.
- Tidak boleh ada business logic kompleks di Model (delegasi ke Service).

### Events & Listeners
- Digunakan untuk: audit log otomatis, notifikasi anomaly, webhook callback.
- Event naming: `[Entity][Action]` contoh: `SaleCreated`, `ShiftClosed`, `StockUpdated`.

---

## 4. Database Schema

Total **9 tabel** production-ready (3NF):

```
categories      → id, name, created_at, updated_at
units           → id, name, created_at, updated_at
suppliers       → id, name, contact, address, created_at, updated_at
products        → id, category_id, unit_id, supplier_id, name, sku, price, stok, created_at, updated_at
users           → id, name, email, password, role(admin|cashier), created_at, updated_at
stock_in        → id, product_id, user_id, qty, note, created_at, updated_at
sales           → id, shift_id, user_id, total, payment_method, payment_status, created_at, updated_at
shifts          → id, user_id, starting_float, ending_cash, expected_cash, variance(GENERATED), status, created_at, updated_at
cash_flow       → id, shift_id, type(in|out), amount, source, created_at, updated_at
audit_log       → id, user_id, action, entity, entity_id, old_data(JSONB), new_data(JSONB), created_at
```

**Database Rules:**
- Semua tabel pakai `timestamps()` (created_at + updated_at).
- `products.stok` di-update via **database trigger** + Eloquent Observer.
- `shifts.variance` pakai **GENERATED ALWAYS AS** PostgreSQL column.
- `audit_log` bersifat immutable: tidak ada update/delete, append-only.
- `sales` dan `shifts` dengan status `closed` tidak boleh dimodifikasi.

---

## 5. Folder Structure

```
app/
├── Domain/
│   ├── Models/              # Eloquent Models (semua model di sini)
│   ├── Services/            # Business logic
│   │   ├── ShiftService.php
│   │   ├── PaymentService.php
│   │   ├── StockService.php
│   │   └── ReconciliationService.php
│   ├── Events/              # SaleCreated, ShiftClosed, dst
│   ├── Listeners/           # AuditLogListener, StockListener
│   └── Repositories/        # Optional, pakai kalau query kompleks
├── Http/
│   ├── Controllers/
│   │   ├── MasterStokController.php
│   │   ├── SalesController.php
│   │   ├── ShiftController.php
│   │   ├── ReportController.php
│   │   └── AuditLogController.php
│   ├── Requests/            # Form Request per action
│   └── Middleware/
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php    # Main layout (sidebar + header + content slot)
│   ├── components/          # Blade components (lihat DESIGN.md Section 6)
│   ├── master-stok/
│   ├── sales/
│   ├── shift/
│   ├── reports/
│   └── audit/
database/
├── migrations/
└── seeders/                 # 20 products, 5 shifts dummy data
routes/
├── web.php
└── api.php                  # Khusus webhook payment gateway
```

---

## 6. JavaScript & UI Interactivity

> ⚠️ WAJIB DIBACA SEBELUM BUAT BLADE VIEW DENGAN INTERAKTIVITAS

**Stack:** Vanilla JavaScript native. Tidak ada framework JS.

### Pattern untuk Modal (CRUD):

```javascript
// BENAR - cara membuka modal
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

// BENAR - cara menutup modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
```

```html
<!-- BENAR - trigger onclick -->
<button onclick="openModal('modal-tambah-produk')">Tambah Produk</button>

<!-- BENAR - modal element -->
<div id="modal-tambah-produk" class="hidden fixed inset-0 ...">
  <!-- form content -->
</div>
```

**DILARANG:**
- ❌ `window.dispatchEvent(new CustomEvent(...))`
- ❌ `document.dispatchEvent(...)`
- ❌ Event bus pattern apapun
- ❌ Import/export ES modules dalam Blade (kecuali lewat Vite yang sudah setup)
- ❌ `x-data`, `x-on`, `@click` (Alpine.js syntax)

### Pattern untuk Form Submit AJAX (kalau perlu):

```javascript
// Gunakan fetch() native
async function submitForm(formId, url, method = 'POST') {
    const form = document.getElementById(formId);
    const data = new FormData(form);
    
    const response = await fetch(url, {
        method: method,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: data
    });
    
    const result = await response.json();
    // handle result
}
```

---

## 7. Data Flow

### POS Transaction Flow
```
1. Cashier login → buka shift (ShiftService::openShift)
2. POS screen → search product → tambah ke keranjang (JS array di-page)
3. Checkout → pilih payment method
4. Submit → SalesController::store → SaleService::createSale
5. SaleService → simpan ke sales + sale_items
6. Observer/Trigger → update products.stok
7. Event SaleCreated → AuditLogListener + AnomalyCheckListener
8. Response → redirect ke receipt atau kembali ke POS screen
```

### Shift Reconciliation Flow
```
1. Open shift → simpan starting_float
2. Sepanjang shift → semua transaksi masuk ke cash_flow
3. Close shift → cashier input ending_cash (blind, tanpa lihat expected)
4. ShiftService::closeShift → hitung expected_cash dari sales + cash_flow
5. variance (GENERATED column) = ending_cash - expected_cash
6. Simpan status closed → trigger alert kalau variance ≠ 0
```

---

## 8. Security

| Concern              | Implementasi                                    |
|----------------------|------------------------------------------------|
| Password hashing     | `Hash::make()` (bcrypt)                        |
| Role-based access    | Laravel Gates & Policies (admin / cashier)     |
| Audit trail          | Immutable audit_log via Event/Listener         |
| Payment webhook      | Verify signature Midtrans/Xendit               |
| Rate limiting        | `throttle:` middleware di login & API routes   |
| Input validation     | Form Request (semua input wajib lewat sini)    |
| CSRF                 | Laravel default CSRF token di semua form       |

---

## 9. Issue Reference (MVP v1.0)

| Issue | Scope                                       |
|-------|---------------------------------------------|
| 1     | Database Setup + Migrations + Seeders       |
| 2     | Master Stok (Produk, Kategori, Satuan, Supplier) |
| 3     | Authentication (Login, Logout, Role)        |
| 4     | Sales Transaction (POS Screen + Checkout)   |
| 5     | Shift Reconciliation + Blind Count          |
| 6     | Payment Gateway (Midtrans/Xendit + QRIS)    |
| 7     | Reporting + Audit Log                       |
| 8     | Deployment Setup                            |
| 9     | QA & Security Hardening                     |

---

## 10. Deployment

- `.env.example` ada di repo root
- Migration: `php artisan migrate --force`
- Queue worker wajib running untuk notifikasi & webhook
- Storage: public disk untuk struk PDF (opsional, Issue 9)
- Health check endpoint: `GET /api/health`
