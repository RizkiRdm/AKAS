{{-- resources/views/components/global-search.blade.php --}}
<div class="relative w-96">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
    </div>
    <input type="text" id="global-search-input"
        class="focus:border-accent focus:ring-accent/30 w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-2 pl-10 text-sm text-slate-100 placeholder-slate-500 focus:ring-2"
        placeholder="Cari produk, shift, laporan... (Ctrl+K)" autocomplete="off"
        oninput="handleGlobalSearch(this.value)">
    <div id="global-search-results"
        class="absolute left-0 right-0 top-full z-50 mt-1 hidden max-h-64 overflow-y-auto rounded-xl border border-slate-700 bg-slate-800 shadow-xl">
    </div>
</div>
