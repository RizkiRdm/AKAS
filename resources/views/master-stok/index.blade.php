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
        <div class="flex flex-col gap-4 md:flex-row">
            <div class="flex-1">
                <form id="search-form" class="flex gap-2">
                    <input type="text" id="search-input" name="search" value="{{ request('search') }}"
                        placeholder="Cari produk atau SKU..."
                        class="focus:border-accent focus:ring-accent/20 flex-1 rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-100 focus:ring-2">
                    <select name="category_id" id="category-filter"
                        class="focus:border-accent focus:ring-accent/20 rounded-lg border border-slate-700 bg-slate-800 px-4 py-2 text-sm text-slate-100 focus:ring-2">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Products Table --}}
        <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-800">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="px-4 py-3 text-left font-medium text-slate-400">SKU</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-400">Nama Produk</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-400">Kategori</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-400">Satuan</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-400">Harga</th>
                        <th class="px-4 py-3 text-center font-medium text-slate-400">Stok</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700" id="product-table-body">
                    @include('master-stok._table_rows', ['products' => $products])
                </tbody>
            </table>
            @if ($products->hasPages())
                <div class="border-t border-slate-700 px-4 py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        {{-- Other Sections (Categories, Units, Suppliers) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Categories --}}
            <div class="rounded-xl border border-slate-700 bg-slate-800 p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-slate-100">Kategori</h3>
                    <button onclick="openModal('modal-category')" class="text-accent text-sm">+ Tambah</button>
                </div>
                <div class="space-y-2">
                    @foreach ($categories as $cat)
                        <div class="flex items-center justify-between border-b border-slate-700/50 py-2 last:border-0">
                            <span class="text-sm text-slate-300">{{ $cat->name }}</span>
                            <button onclick="editCategory({{ $cat->id }}, '{{ $cat->name }}')"
                                class="hover:text-accent text-xs text-slate-500">Edit</button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Units --}}
            <div class="rounded-xl border border-slate-700 bg-slate-800 p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-slate-100">Satuan</h3>
                    <button onclick="openModal('modal-unit')" class="text-accent text-sm">+ Tambah</button>
                </div>
                <div class="space-y-2">
                    @foreach ($units as $unit)
                        <div class="flex items-center justify-between border-b border-slate-700/50 py-2 last:border-0">
                            <span class="text-sm text-slate-300">{{ $unit->name }}</span>
                            <button class="hover:text-accent text-xs text-slate-500">Edit</button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Suppliers --}}
            <div class="rounded-xl border border-slate-700 bg-slate-800 p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-slate-100">Supplier</h3>
                    <button onclick="openModal('modal-supplier')" class="text-accent text-sm">+ Tambah</button>
                </div>
                <div class="space-y-2">
                    @foreach ($suppliers as $sup)
                        <div class="flex items-center justify-between border-b border-slate-700/50 py-2 last:border-0">
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-300">{{ $sup->name }}</span>
                                <span class="text-xs text-slate-500">{{ $sup->contact }}</span>
                            </div>
                            <button class="hover:text-accent text-xs text-slate-500">Edit</button>
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
            <x-form-input name="sku" label="SKU" placeholder="Kosongkan untuk otomatis" id="input-sku" />
            <x-form-input name="name" label="Nama Produk" placeholder="Masukkan nama produk" required id="input-name" />
            <x-form-input name="initial_stock" label="Stok Awal" type="number" placeholder="0" id="input-initial-stock" />

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-300">Kategori</label>
                    <select name="category_id" id="input-category-id"
                        class="focus:border-accent focus:ring-accent/20 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:ring-2"
                        required>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-300">Satuan</label>
                    <select name="unit_id" id="input-unit-id"
                        class="focus:border-accent focus:ring-accent/20 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:ring-2"
                        required>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-form-input name="price" label="Harga Jual" type="number" placeholder="0" required
                    id="input-price" />
                <x-form-input name="purchase_price" label="Harga Beli" type="number" placeholder="0" required
                    id="input-purchase-price" />
            </div>
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-300">Supplier</label>
                <select name="supplier_id" id="input-supplier-id"
                    class="focus:border-accent focus:ring-accent/20 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:ring-2">
                    <option value="">Tanpa Supplier</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-product')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
                <x-pos-button type="submit">Simpan Produk</x-pos-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Konfirmasi Hapus Produk --}}
    <x-modal id="modal-delete-product" title="Hapus Produk">
        <form id="form-delete-product" method="POST" action="">
            @csrf
            @method('DELETE')
            <p class="mb-6 text-slate-300">Apakah Anda yakin ingin menghapus produk <span id="delete-product-name"
                    class="font-bold text-slate-100"></span>? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-delete-product')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
                <button type="submit"
                    class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm text-red-400 hover:bg-red-500/20">Hapus
                    Permanen</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Stok Masuk --}}
    <x-modal id="modal-stock-in" title="Stok Masuk">
        <form method="POST" action="{{ route('master-stok.stock-in.store') }}">
            @csrf
            <div class="mb-4">
                <label class="mb-1 block text-sm font-medium text-slate-300">Pilih Produk</label>
                <select name="product_id"
                    class="focus:border-accent focus:ring-accent/20 w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:ring-2"
                    required>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (Stok:
                            {{ $p->stok }})</option>
                    @endforeach
                </select>
            </div>
            <x-form-input name="qty" label="Jumlah Masuk" type="number" placeholder="Masukkan jumlah" required />
            <x-form-input name="note" label="Catatan (Opsional)" placeholder="Contoh: Restock dari gudang" />

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-stock-in')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
                <x-pos-button type="submit">Tambah Stok</x-pos-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Kategori --}}
    <x-modal id="modal-category" title="Form Kategori">
        <form id="form-category" method="POST" action="{{ route('master-stok.categories.store') }}">
            @csrf
            <div id="method-field-category"></div>
            <x-form-input name="name" label="Nama Kategori" placeholder="Contoh: Minuman" required
                id="input-cat-name" />
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-category')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
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
                <button type="button" onclick="closeModal('modal-unit')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
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
                <button type="button" onclick="closeModal('modal-supplier')"
                    class="rounded-lg border border-slate-700 bg-transparent px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Batal</button>
                <x-pos-button type="submit">Simpan Supplier</x-pos-button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        // AJAX Search
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const tableBody = document.getElementById('product-table-body');

        const performSearch = () => {
            const formData = new URLSearchParams(new FormData(searchForm));
            fetch(`/master-stok?${formData.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => tableBody.innerHTML = html);
        };

        searchInput.addEventListener('input', performSearch);
        categoryFilter.addEventListener('change', performSearch);

        // Frontend validation
        const productForm = document.getElementById('form-product');
        productForm.addEventListener('submit', function(e) {
            const price = parseFloat(document.getElementById('input-price').value);
            const purchasePrice = parseFloat(document.getElementById('input-purchase-price').value);
            
            if (price <= purchasePrice) {
                e.preventDefault();
                alert('Harga jual harus lebih tinggi dari harga beli!');
            }
        });

        function openAddProductModal() {
            productForm.reset();
            productForm.action = "{{ route('master-stok.products.store') }}";
            document.getElementById('method-field-product').innerHTML = '';
            document.getElementById('input-initial-stock').parentElement.style.display = 'block';
            openModal('modal-product');
        }

        function editProduct(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;

            const fields = {
                'input-name': row.dataset.name,
                'input-sku': row.dataset.sku,
                'input-category-id': row.dataset.categoryId,
                'input-unit-id': row.dataset.unitId,
                'input-supplier-id': row.dataset.supplierId || '',
                'input-price': row.dataset.price,
                'input-purchase-price': row.dataset.purchasePrice
                // stok awal tidak diisi saat edit
            };

            Object.entries(fields).forEach(([id, value]) => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            });

            document.getElementById('input-initial-stock').parentElement.style.display = 'none';

            productForm.action = `/master-stok/products/${id}`;
            document.getElementById('method-field-product').innerHTML = '@method('PUT')';

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
            document.getElementById('method-field-category').innerHTML = '@method('PUT')';
            openModal('modal-category');
        }
    </script>
@endpush
