@extends('layouts.app')

{{-- Reference: DESIGN.md & ARCHITECTURE.md --}}

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-100">Master Stok</h1>
        <div class="flex gap-2">
            <x-pos-button onclick="openModal('modal-stock-in')">
                + Stok Masuk
            </x-pos-button>
            <x-pos-button onclick="openAddProductModal()">
                + Tambah Produk
            </x-pos-button>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <form action="{{ route('master-stok.index') }}" method="GET" class="flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari produk atau SKU..." 
                    class="flex-1 bg-secondary text-slate-100 border border-slate-700 rounded-lg px-4 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20"
                >
                <select name="category_id" class="bg-secondary text-slate-100 border border-slate-700 rounded-lg px-4 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <x-pos-button type="submit">Filter</x-pos-button>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-secondary rounded-xl border border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">SKU</th>
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">Nama Produk</th>
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">Kategori</th>
                    <th class="px-4 py-3 text-left text-slate-400 font-medium">Satuan</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Harga</th>
                    <th class="px-4 py-3 text-center text-slate-400 font-medium">Stok</th>
                    <th class="px-4 py-3 text-right text-slate-400 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700">
                @forelse($products as $product)
                <tr class="hover:bg-slate-700/50 transition-none" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}" data-category-id="{{ $product->category_id }}" data-unit-id="{{ $product->unit_id }}" data-supplier-id="{{ $product->supplier_id }}" data-price="{{ (float)$product->price }}">
                    <td class="px-4 py-3 text-slate-300 font-mono">{{ $product->sku }}</td>
                    <td class="px-4 py-3 text-slate-100 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ $product->category->name }}</td>
                    <td class="px-4 py-3 text-slate-300">{{ $product->unit->name }}</td>
                    <td class="px-4 py-3 text-right text-slate-100 font-mono">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $product->stok < 10 ? 'bg-red-400/10 text-red-400' : 'bg-green-400/10 text-green-400' }}">
                            {{ $product->stok }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <button onclick="editProduct({{ $product->id }})" class="text-accent hover:text-cyan-300">Edit</button>
                        <button onclick="confirmDeleteProduct({{ $product->id }}, '{{ $product->name }}')" class="text-red-400 hover:text-red-300">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500 italic">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="px-4 py-3 border-t border-slate-700">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- Other Sections (Categories, Units, Suppliers) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Categories --}}
        <div class="bg-secondary rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-slate-100">Kategori</h3>
                <button onclick="openModal('modal-category')" class="text-accent text-sm">+ Tambah</button>
            </div>
            <div class="space-y-2">
                @foreach($categories as $cat)
                <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
                    <span class="text-sm text-slate-300">{{ $cat->name }}</span>
                    <button onclick="editCategory({{ $cat->id }}, '{{ $cat->name }}')" class="text-xs text-slate-500 hover:text-accent">Edit</button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Units --}}
        <div class="bg-secondary rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-slate-100">Satuan</h3>
                <button onclick="openModal('modal-unit')" class="text-accent text-sm">+ Tambah</button>
            </div>
            <div class="space-y-2">
                @foreach($units as $unit)
                <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
                    <span class="text-sm text-slate-300">{{ $unit->name }}</span>
                    <button class="text-xs text-slate-500 hover:text-accent">Edit</button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Suppliers --}}
        <div class="bg-secondary rounded-xl border border-slate-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-slate-100">Supplier</h3>
                <button onclick="openModal('modal-supplier')" class="text-accent text-sm">+ Tambah</button>
            </div>
            <div class="space-y-2">
                @foreach($suppliers as $sup)
                <div class="flex items-center justify-between py-2 border-b border-slate-700/50 last:border-0">
                    <div class="flex flex-col">
                        <span class="text-sm text-slate-300">{{ $sup->name }}</span>
                        <span class="text-xs text-slate-500">{{ $sup->contact }}</span>
                    </div>
                    <button class="text-xs text-slate-500 hover:text-accent">Edit</button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}

{{-- Modal Produk --}}
<x-modal id="modal-product" title="Form Produk">
    <form id="form-product" method="POST" action="{{ route('master-stok.products.store') }}">
        @csrf
        <div id="method-field-product"></div>
        <x-form-input name="sku" label="SKU" placeholder="Contoh: BRG-001" required id="input-sku" />
        <x-form-input name="name" label="Nama Produk" placeholder="Masukkan nama produk" required id="input-name" />
        
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-1">Kategori</label>
                <select name="category_id" id="input-category-id" class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-1">Satuan</label>
                <select name="unit_id" id="input-unit-id" class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20" required>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-form-input name="price" label="Harga Jual" type="number" placeholder="0" required id="input-price" />
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-1">Supplier</label>
                <select name="supplier_id" id="input-supplier-id" class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20">
                    <option value="">Tanpa Supplier</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-product')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <x-pos-button type="submit">Simpan Produk</x-pos-button>
        </div>
    </form>
</x-modal>

{{-- Modal Konfirmasi Hapus Produk --}}
<x-modal id="modal-delete-product" title="Hapus Produk">
    <form id="form-delete-product" method="POST" action="">
        @csrf
        @method('DELETE')
        <p class="text-slate-300 mb-6">Apakah Anda yakin ingin menghapus produk <span id="delete-product-name" class="font-bold text-slate-100"></span>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-delete-product')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <button type="submit" class="bg-red-500/10 text-red-400 border border-red-500/30 px-4 py-2 rounded-lg text-sm hover:bg-red-500/20">Hapus Permanen</button>
        </div>
    </form>
</x-modal>

{{-- Modal Stok Masuk --}}
<x-modal id="modal-stock-in" title="Stok Masuk">
    <form method="POST" action="{{ route('master-stok.stock-in.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-300 mb-1">Pilih Produk</label>
            <select name="product_id" class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20" required>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Stok: {{ $p->stok }})</option>
                @endforeach
            </select>
        </div>
        <x-form-input name="qty" label="Jumlah Masuk" type="number" placeholder="Masukkan jumlah" required />
        <x-form-input name="note" label="Catatan (Opsional)" placeholder="Contoh: Restock dari gudang" />
        
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-stock-in')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <x-pos-button type="submit">Tambah Stok</x-pos-button>
        </div>
    </form>
</x-modal>

{{-- Modal Kategori --}}
<x-modal id="modal-category" title="Form Kategori">
    <form id="form-category" method="POST" action="{{ route('master-stok.categories.store') }}">
        @csrf
        <div id="method-field-category"></div>
        <x-form-input name="name" label="Nama Kategori" placeholder="Contoh: Minuman" required id="input-cat-name" />
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-category')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <x-pos-button type="submit">Simpan Kategori</x-pos-button>
        </div>
    </form>
</x-modal>

{{-- Modal Satuan --}}
<x-modal id="modal-unit" title="Tambah Satuan">
    <form method="POST" action="{{ route('master-stok.units.store') }}">
        @csrf
        <x-form-input name="name" label="Nama Satuan" placeholder="Contoh: Pcs, Box, Kg" required />
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-unit')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <x-pos-button type="submit">Simpan Satuan</x-pos-button>
        </div>
    </form>
</x-modal>

{{-- Modal Supplier --}}
<x-modal id="modal-supplier" title="Tambah Supplier">
    <form method="POST" action="{{ route('master-stok.suppliers.store') }}">
        @csrf
        <x-form-input name="name" label="Nama Supplier" required />
        <x-form-input name="contact" label="Kontak" />
        <x-form-input name="address" label="Alamat" />
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" onclick="closeModal('modal-supplier')" class="bg-transparent text-slate-300 border border-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-700">Batal</button>
            <x-pos-button type="submit">Simpan Supplier</x-pos-button>
        </div>
    </form>
</x-modal>

@endsection

@push('scripts')
<script>
    function openAddProductModal() {
        document.getElementById('form-product').reset();
        document.getElementById('form-product').action = "{{ route('master-stok.products.store') }}";
        document.getElementById('method-field-product').innerHTML = '';
        openModal('modal-product');
    }

    function editProduct(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;

        document.getElementById('input-name').value = row.dataset.name;
        document.getElementById('input-sku').value = row.dataset.sku;
        document.getElementById('input-category-id').value = row.dataset.categoryId;
        document.getElementById('input-unit-id').value = row.dataset.unitId;
        document.getElementById('input-supplier-id').value = row.dataset.supplierId || '';
        document.getElementById('input-price').value = row.dataset.price;

        document.getElementById('form-product').action = `/master-stok/products/${id}`;
        document.getElementById('method-field-product').innerHTML = '@method("PUT")';
        
        openModal('modal-product');
    }

    function confirmDeleteProduct(id, name) {
        document.getElementById('delete-product-name').textContent = name;
        document.getElementById('form-delete-product').action = `/master-stok/products/${id}`;
        openModal('modal-delete-product');
    }

    function editCategory(id, name) {
        document.getElementById('input-cat-name').value = name;
        document.getElementById('form-category').action = `/master-stok/categories/${id}`;
        document.getElementById('method-field-category').innerHTML = '@method("PUT")';
        openModal('modal-category');
    }
</script>
@endpush
