**Project Name:** Modern POS
**Version:** 1.0 (MVP)  
**Last Updated:** 2026-04-13  
**Purpose:** Single source of truth untuk UI/UX. Semua Blade template harus mengikuti dokumen ini agar konsisten & profesional.

## 1. Design Philosophy
- **Desktop-first** (app hanya dijalankan di desktop/laptop, bukan mobile).
- Fungsi > estetik (clean, fast, no clutter).
- Dark mode only (tidak ada light mode).
- Tailwind CSS utility-first + reusable Blade components.
- No animation kecuali focus state (ring + subtle scale 105%).
- Modals hanya untuk input/confirmation (tidak untuk display info).

## 2. Color System (60-30-10 Rule – Dark Mode)
Mengikuti 60-30-10 rule untuk keseimbangan visual:

- **60% Dominant (Background utama)**: `#0f172a` (slate-900)  
  Digunakan untuk: body, main container, header background.
- **30% Secondary (Panels & Cards)**: `#1e2937` (slate-800)  
  Digunakan untuk: sidebar, cards, tables, modals.
- **10% Accent (Action & Highlight)**: `#22d3ee` (cyan-400)  
  Digunakan untuk: primary buttons, links, focus rings, alerts, variance negative, active menu.

**Text & Neutral:**
- Text primary: `#f1f5f9` (slate-100)
- Text secondary: `#cbd5e1` (slate-300)
- Border: `#334155` (slate-700)
- Success: `#4ade80` (green-400) – hanya untuk status OK
- Danger: `#f87171` (red-400) – untuk variance negatif / error

Tailwind config snippet (tambahkan di `tailwind.config.js`):

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
## 3. Typography
- Font family: `system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif` (default Tailwind sans).
- Heading 1: `text-3xl font-semibold tracking-tight`
- Heading 2: `text-2xl font-medium`
- Body text: `text-base` (16px)
- Small text: `text-sm`
- All text menggunakan slate-100 / slate-300 untuk readability (kontras > 4.5:1).

## 4. Overall Layout (Desktop-only)
Struktur fixed & konsisten di seluruh halaman:

```html
<div class="flex h-screen bg-dominant">
  <!-- LEFT SIDEBAR (fixed 240px) -->
  <aside class="w-60 bg-secondary border-r border-slate-700 flex flex-col">
    <!-- Logo + Menu -->
  </aside>

  <!-- MAIN AREA -->
  <div class="flex-1 flex flex-col">
    <!-- TOP HEADER -->
    <header class="h-14 bg-dominant border-b border-slate-700 px-6 flex items-center justify-between">
      <!-- Global Search -->
      <!-- Shift status -->
      <!-- User avatar + logout -->
    </header>

    <!-- CONTENT AREA -->
    <main class="flex-1 overflow-auto p-6 bg-dominant">
      <!-- Page content -->
    </main>
  </div>
</div>
```

- Sidebar: navigation utama (Master Stok, Transaksi, Shift, Reports, Audit).
- Header: selalu ada global search + info shift aktif.
- Content: full width dengan padding 1.5rem.

## 5. Global Search (Fitur Wajib)
- Letak: tengah header, lebar 400px.
- Design:
  - Input dengan icon search (heroicons magnifier).
  - Placeholder: "Cari produk, shift, laporan... (Ctrl+K)"
  - Saat diketik → dropdown hasil real-time (products, shifts, reports) di bawah input.
  - Hasil ditampilkan dalam list sederhana (nama + tipe).
  - Gunakan Blade component `global-search.blade.php`.

Tailwind example:
```html
<div class="relative w-96">
  <input 
    type="text" 
    class="w-full bg-secondary text-slate-100 border border-slate-600 focus:border-accent focus:ring-2 focus:ring-accent/30 rounded-xl px-4 py-2 pl-10"
    placeholder="Cari produk atau shift...">
</div>
```

## 6. Reusable Blade Components (resources/views/components/)
Buat folder `components/` dengan:
- `pos-button.blade.php` (primary accent)
- `data-table.blade.php`
- `modal.blade.php` (backdrop + centered card)
- `card.blade.php`
- `form-input.blade.php`
- `alert.blade.php` (success/danger)

Modal example (hanya untuk input):
```html
<div class="fixed inset-0 bg-black/70 flex items-center justify-center">
  <div class="bg-secondary rounded-2xl w-[480px] p-6">
    <!-- form content -->
  </div>
</div>
```

## 7. Page-Specific UI Guidelines
- **POS Transaction Screen**: Large product grid / search bar besar + keranjang di kanan (split layout).
- **Shift Reconciliation**: Clean form dengan dua kolom (expected hidden sampai blind count selesai).
- **Master Stok**: Table + filter + add button di header.
- **Dashboard**: Grid cards (sales hari ini, variance, low stock).
- **Audit Log**: Simple table dengan filter tanggal.

Semua table pakai `bg-secondary border-slate-700` + hover:bg-slate-700.

## 8. Focus & Interaction Rules
- Hanya focus state: `focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-dominant`
- Button hover: `hover:bg-accent/10 hover:text-accent`
- No transition/animation kecuali yang di atas.

## 9. Accessibility & Performance
- Kontras teks minimal 4.5:1.
- Semua form label + aria-label.
- Keyboard navigation (Tab + Enter).
- Load time < 1s (Tailwind purge + minimal DOM).

---

**Dokumen ini wajib dibaca sebelum generate Blade view.**  
Setiap Blade file harus reference DESIGN.md di comment atasnya.  
Kalau ada perubahan desain, update file ini dulu.
