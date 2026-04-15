# DESIGN.md

**Project Name:** AKAS — Modern POS
**Version:** 1.0 (MVP)
**Last Updated:** 2026-04-15
**Purpose:** Single source of truth untuk semua Blade templates dan UI behavior.
**Audience:** AI agent, solo developer

---

> ⚠️ CRITICAL RULES FOR AI AGENT
> 1. Setiap halaman yang punya CRUD → semua operasi (tambah, edit, hapus) **wajib di halaman yang sama via modal**.
> 2. **Dilarang membuat tab, accordion navigation, atau halaman terpisah** untuk operasi CRUD kecuali ada instruksi eksplisit di issue.
> 3. Modal trigger wajib pakai `onclick="openModal('modal-id')"` — bukan dispatch, bukan event bus.
> 4. Dark mode only. Jangan ada class `light` atau conditional theme.
> 5. Kalau ragu soal layout → lihat Section 4 dan ikuti struktur HTML yang sudah diberikan.

---

## 1. Design Philosophy

- **Desktop-first** — app hanya dijalankan di desktop/laptop. Tidak ada mobile breakpoint.
- **Fungsi > Estetik** — clean, fast, no clutter, no decorative elements.
- **Dark mode only** — tidak ada light mode, tidak ada toggle tema.
- **Tailwind utility-first** — pakai class Tailwind langsung, tidak ada custom CSS kecuali Tailwind config.
- **Minimal animation** — hanya `focus:ring` dan `hover` state. Tidak ada transition, fade, atau slide.
- **Modal untuk CRUD** — semua form tambah/edit/hapus pakai modal di halaman yang sama.
- **Tidak ada tab navigasi** untuk memisahkan CRUD dalam satu domain (contoh: tab "Produk" vs tab "Kategori" dalam satu halaman Master Stok adalah **salah**).

---

## 2. Color System (60-30-10)

| Peran          | Hex       | Tailwind       | Digunakan untuk                              |
|----------------|-----------|----------------|----------------------------------------------|
| 60% Dominant   | `#0f172a` | `slate-900`    | Body, main container, header bg              |
| 30% Secondary  | `#1e2937` | `slate-800`    | Sidebar, cards, tables, modals               |
| 10% Accent     | `#22d3ee` | `cyan-400`     | Primary buttons, links, focus rings, aktif   |
| Text primary   | `#f1f5f9` | `slate-100`    | Semua teks utama                             |
| Text secondary | `#cbd5e1` | `slate-300`    | Label, placeholder, subtext                  |
| Border         | `#334155` | `slate-700`    | Semua border                                 |
| Success        | `#4ade80` | `green-400`    | Status OK, variance zero                     |
| Danger         | `#f87171` | `red-400`      | Error, variance negatif, delete confirm      |

**Tailwind config** (`tailwind.config.js`):
```js
theme: {
  extend: {
    colors: {
      dominant: '#0f172a',
      secondary: '#1e2937',
      accent: '#22d3ee',
    }
  }
}
```

---

## 3. Typography

| Elemen        | Classes Tailwind                              |
|---------------|-----------------------------------------------|
| Page title    | `text-2xl font-semibold text-slate-100`       |
| Section title | `text-lg font-medium text-slate-100`          |
| Body text     | `text-base text-slate-100`                    |
| Label / meta  | `text-sm text-slate-300`                      |
| Small hint    | `text-xs text-slate-400`                      |

Font: default Tailwind system-ui stack. Tidak perlu Google Fonts.

---

## 4. Layout Struktur (Wajib Diikuti)

Semua halaman menggunakan layout ini via `layouts/app.blade.php`:

```html
{{-- layouts/app.blade.php --}}
<div class="flex h-screen bg-dominant overflow-hidden">

  {{-- SIDEBAR (fixed 240px) --}}
  <aside class="w-60 bg-secondary border-r border-slate-700 flex flex-col flex-shrink-0">
    {{-- Logo --}}
    <div class="h-14 flex items-center px-5 border-b border-slate-700">
      <span class="text-accent font-bold text-lg tracking-tight">AKAS POS</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 py-4 space-y-1 px-3">
      <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-slate-100 {{ request()->is('dashboard') ? 'bg-slate-700 text-slate-100' : '' }}">
        Dashboard
      </a>
      <a href="/master-stok" ...>Master Stok</a>
      <a href="/sales" ...>Transaksi</a>
      <a href="/shift" ...>Shift</a>
      <a href="/reports" ...>Laporan</a>
      <a href="/audit" ...>Audit Log</a>
    </nav>

    {{-- User info bottom --}}
    <div class="p-4 border-t border-slate-700">
      <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
    </div>
  </aside>

  {{-- MAIN AREA --}}
  <div class="flex-1 flex flex-col min-w-0">

    {{-- HEADER --}}
    <header class="h-14 bg-dominant border-b border-slate-700 px-6 flex items-center justify-between flex-shrink-0">
      {{-- Global Search --}}
      <div class="relative w-96">
        @include('components.global-search')
      </div>

      {{-- Right: shift status + user --}}
      <div class="flex items-center gap-4">
        @include('components.shift-status')
        <form method="POST" action="/logout">
          @csrf
          <button type="submit" class="text-sm text-slate-400 hover:text-slate-100">Logout</button>
        </form>
      </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="flex-1 overflow-auto p-6 bg-dominant">
      @yield('content')
    </main>

  </div>
</div>
```

---

## 5. Global Search

- Posisi: tengah-kiri header, lebar `w-96` (384px).
- Placeholder: `"Cari produk, shift, laporan... (Ctrl+K)"`
- Hasil real-time via `fetch()` dropdown di bawah input.
- Komponen: `resources/views/components/global-search.blade.php`

```html
{{-- components/global-search.blade.php --}}
<div class="relative w-96">
  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
    <svg class="w-4 h-4 text-slate-400" ...></svg>
  </div>
  <input
    type="text"
    id="global-search-input"
    class="w-full bg-secondary text-slate-100 border border-slate-700 focus:border-accent focus:ring-2 focus:ring-accent/30 rounded-xl px-4 py-2 pl-10 text-sm placeholder-slate-500"
    placeholder="Cari produk, shift, laporan... (Ctrl+K)"
    autocomplete="off"
    oninput="handleGlobalSearch(this.value)"
  >
  <div id="global-search-results" class="hidden absolute top-full left-0 right-0 mt-1 bg-secondary border border-slate-700 rounded-xl shadow-xl z-50 max-h-64 overflow-y-auto">
  </div>
</div>
```

---

## 6. Reusable Blade Components

Semua komponen ada di `resources/views/components/`:

| File                      | Kegunaan                                    |
|---------------------------|---------------------------------------------|
| `pos-button.blade.php`    | Primary button (accent color)               |
| `danger-button.blade.php` | Delete/destructive action button            |
| `data-table.blade.php`    | Table dengan header + row slot              |
| `modal.blade.php`         | Modal wrapper (backdrop + centered card)    |
| `card.blade.php`          | Panel/card container                        |
| `form-input.blade.php`    | Input dengan label + error message          |
| `alert.blade.php`         | Success/danger flash message                |
| `badge.blade.php`         | Status badge (success/danger/warning)       |
| `global-search.blade.php` | Header search component                     |
| `shift-status.blade.php`  | Shift aktif indicator di header             |

### Modal Component (WAJIB pakai ini untuk semua CRUD form):

```html
{{-- components/modal.blade.php --}}
{{-- Props: id, title --}}
<div id="{{ $id }}" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
  <div class="bg-secondary rounded-2xl w-[520px] max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-700">
    {{-- Modal header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
      <h3 class="text-lg font-medium text-slate-100">{{ $title }}</h3>
      <button onclick="closeModal('{{ $id }}')" class="text-slate-400 hover:text-slate-100">
        <svg class="w-5 h-5" ...></svg>
      </button>
    </div>
    {{-- Modal body --}}
    <div class="p-6">
      {{ $slot }}
    </div>
  </div>
</div>
```

### JavaScript untuk Modal (letakkan di layout atau script per-page):

```javascript
// Wajib ada di setiap halaman yang pakai modal
function openModal(id) {
  document.getElementById(id).classList.remove('hidden');
  document.getElementById(id).classList.add('flex');
}

function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
  document.getElementById(id).classList.remove('flex');
}

// Tutup modal kalau klik backdrop
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
    e.target.classList.add('hidden');
    e.target.classList.remove('flex');
  }
});
```

---

## 7. Page-Specific UI Guidelines

> ⚠️ Setiap halaman CRUD: **SEMUA operasi tambah, edit, hapus ada di satu halaman yang sama via modal.**
> Tidak boleh redirect ke halaman terpisah untuk form tambah/edit.

---

### 7.1 Dashboard (`/dashboard`)

**Layout:** Grid 4 kolom kartu statistik di atas, tabel recent transactions di bawah.

```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│ Penjualan   │ Transaksi   │ Stok Kritis │ Variance    │
│ Hari Ini    │ Hari Ini    │ (< 10 unit) │ Shift Aktif │
├─────────────┴─────────────┴─────────────┴─────────────┤
│ Transaksi Terbaru (tabel, 10 baris)                    │
└────────────────────────────────────────────────────────┘
```

Kartu statistik: `bg-secondary border border-slate-700 rounded-xl p-5`

---

### 7.2 Master Stok (`/master-stok`)

**Layout:** Satu halaman. Semua CRUD lewat modal.

```
┌─────────────────────────────────────────────────────────┐
│ [Page Title: Master Stok]          [+ Tambah Produk]    │
├──────────────────────┬──────────────────────────────────┤
│ Filter: Kategori ▾   │ Search: [___________________]    │
├──────────────────────┴──────────────────────────────────┤
│ TABEL PRODUK                                            │
│ No | Nama | SKU | Kategori | Satuan | Harga | Stok |Aksi│
│ ...                                        [Edit][Hapus]│
├─────────────────────────────────────────────────────────┤
│ Pagination                                              │
└─────────────────────────────────────────────────────────┘

[MODAL] Tambah/Edit Produk → id: "modal-produk"
[MODAL] Konfirmasi Hapus   → id: "modal-hapus-produk"
```

**Trigger modal:**
```html
{{-- Tambah --}}
<button onclick="openModal('modal-produk')">+ Tambah Produk</button>

{{-- Edit (dari baris tabel, lewat JS populate form) --}}
<button onclick="editProduk({{ $produk->id }})">Edit</button>

{{-- Hapus --}}
<button onclick="openHapusModal({{ $produk->id }}, '{{ $produk->nama }}')">Hapus</button>
```

**Pola edit (populate modal dengan data existing):**
```javascript
function editProduk(id) {
  // Fetch data produk via AJAX atau gunakan data-attribute
  const row = document.querySelector(`[data-id="${id}"]`);
  document.getElementById('input-nama').value = row.dataset.nama;
  document.getElementById('input-harga').value = row.dataset.harga;
  // dst...
  document.getElementById('form-produk').action = `/master-stok/${id}`;
  document.getElementById('method-field').value = 'PUT';
  openModal('modal-produk');
}
```

> **Kategori, Satuan, Supplier** → masing-masing punya section sendiri di halaman yang sama dengan tabel dan modalnya. **Tidak dipisahkan ke halaman/tab tersendiri.**

---

### 7.3 POS Transaction Screen (`/sales/pos`)

**Layout:** Split dua kolom (60/40).

```
┌──────────────────────────────┬─────────────────────────┐
│ KIRI (60%): Product Area     │ KANAN (40%): Keranjang  │
│                              │                         │
│ Search produk [_________]    │ Item 1 ... Rp xx.xxx    │
│                              │ Item 2 ... Rp xx.xxx    │
│ Grid produk (3 kolom card)   │ ─────────────────────── │
│ [Produk A] [Produk B] ...    │ Subtotal: Rp xxx.xxx    │
│                              │ Diskon:   Rp 0          │
│                              │ TOTAL:    Rp xxx.xxx    │
│                              │                         │
│                              │ [BAYAR TUNAI]           │
│                              │ [BAYAR QRIS]            │
│                              │ [BAYAR TRANSFER]        │
└──────────────────────────────┴─────────────────────────┘
```

Keranjang dikelola sebagai JavaScript array di page:
```javascript
let cart = []; // { id, nama, harga, qty, subtotal }
```

---

### 7.4 Shift Reconciliation (`/shift`)

**Layout:** Dua panel — panel kiri (daftar shift), panel kanan (detail/form).

```
┌──────────────────┬─────────────────────────────────────┐
│ Shift History    │ [Buka Shift Baru / Detail Shift]    │
│                  │                                     │
│ Shift #45  ✓    │ Form blind count:                   │
│ Shift #44  ✓    │ Modal kas awal (starting_float)     │
│ Shift #43  ✗    │ Input kas akhir (ending_cash)       │
│ ...             │ Variance: [tersembunyi sampai submit]│
└──────────────────┴─────────────────────────────────────┘
```

**Blind count rule:** Field `expected_cash` dan `variance` **tidak boleh ditampilkan** sampai cashier submit `ending_cash`. Gunakan `hidden` atau conditional Blade.

---

### 7.5 Laporan (`/reports`)

**Layout:** Filter bar di atas, konten laporan di bawah.

```
┌─────────────────────────────────────────────────────────┐
│ Filter: [Tanggal Awal] [Tanggal Akhir] [Tipe ▾] [Cari] │
├─────────────────────────────────────────────────────────┤
│ Summary cards (Total, Jumlah Transaksi, dll)            │
├─────────────────────────────────────────────────────────┤
│ Tabel detail transaksi                                  │
│                                      [Export PDF/CSV]   │
└─────────────────────────────────────────────────────────┘
```

---

### 7.6 Audit Log (`/audit`)

**Layout:** Filter + tabel simple. Read-only, tidak ada action button selain view detail.

```
┌─────────────────────────────────────────────────────────┐
│ Filter: [Tanggal] [User ▾] [Aksi ▾]          [Filter]  │
├─────────────────────────────────────────────────────────┤
│ Tabel: Waktu | User | Aksi | Entity | ID | [Detail]     │
└─────────────────────────────────────────────────────────┘
```

Detail audit (old/new data JSONB) tampil di modal read-only.

---

## 8. Table Styling (Standard)

Semua tabel pakai pattern ini:

```html
<div class="bg-secondary rounded-xl border border-slate-700 overflow-hidden">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-slate-700">
        <th class="px-4 py-3 text-left text-slate-400 font-medium">Nama</th>
        {{-- ... --}}
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-700">
      <tr class="hover:bg-slate-700/50 transition-none">
        <td class="px-4 py-3 text-slate-100">...</td>
      </tr>
    </tbody>
  </table>
</div>
```

---

## 9. Form & Input Styling (Standard)

```html
{{-- Label --}}
<label class="block text-sm font-medium text-slate-300 mb-1">Nama Produk</label>

{{-- Input --}}
<input
  type="text"
  class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/20 placeholder-slate-500"
  placeholder="Masukkan nama produk"
>

{{-- Error message --}}
@error('nama')
  <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
@enderror

{{-- Select --}}
<select class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20">
  <option value="">Pilih kategori...</option>
</select>
```

---

## 10. Button Styling (Standard)

```html
{{-- Primary (Accent) --}}
<button class="bg-accent text-slate-900 font-semibold px-4 py-2 rounded-lg text-sm hover:bg-cyan-300 focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-dominant">
  Simpan
</button>

{{-- Secondary (Ghost) --}}
<button class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700 hover:text-slate-100">
  Batal
</button>

{{-- Danger --}}
<button class="bg-red-500/10 text-red-400 border border-red-500/30 px-4 py-2 rounded-lg text-sm hover:bg-red-500/20">
  Hapus
</button>
```

---

## 11. Focus & Interaction

- Focus ring: `focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-dominant`
- Hover background: `hover:bg-slate-700/50`
- Active/selected menu: `bg-slate-700 text-slate-100`
- **Tidak ada transition/animation** kecuali explicit di atas.

---

## 12. Flash Message & Alerts

Gunakan session flash dari Laravel:

```html
@if(session('success'))
  <div class="mb-4 px-4 py-3 bg-green-400/10 border border-green-400/30 rounded-lg text-green-400 text-sm">
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="mb-4 px-4 py-3 bg-red-400/10 border border-red-400/30 rounded-lg text-red-400 text-sm">
    {{ session('error') }}
  </div>
@endif
```

---

## 13. Checklist AI Agent Sebelum Generate Blade

Sebelum generate view apapun, pastikan:

- [ ] Apakah halaman ini butuh CRUD? → Semua operasi di halaman yang sama via modal.
- [ ] Apakah ada tab navigasi dalam satu domain? → Hapus, jadikan satu halaman.
- [ ] Apakah ada onclick yang pakai dispatch? → Ganti dengan `openModal('id')`.
- [ ] Apakah semua input pakai class dari Section 9?
- [ ] Apakah semua button pakai class dari Section 10?
- [ ] Apakah layout menggunakan `layouts/app.blade.php` dengan `@yield('content')`?
- [ ] Apakah ada comment `{{-- Reference: DESIGN.md --}}` di baris pertama view?

---

**Dokumen ini wajib dibaca SEBELUM generate Blade view.**
Update dokumen ini jika ada perubahan desain sebelum implementasi.
