{{-- resources/views/components/global-search.blade.php --}}
<div class="relative w-96">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
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
