@forelse($products as $product)
    <tr data-id="{{ $product->id }}" 
        data-name="{{ $product->name }}"
        data-sku="{{ $product->sku }}" 
        data-category-id="{{ $product->category_id }}"
        data-unit-id="{{ $product->unit_id }}" 
        data-supplier-id="{{ $product->supplier_id }}"
        data-price="{{ $product->price }}"
        data-purchase-price="{{ $product->purchase_price ?? 0 }}"
        class="hover:bg-slate-900/80">
        <td class="px-4 py-3 font-mono text-slate-300">{{ $product->sku }}</td>
        <td class="px-4 py-3 font-medium text-slate-100">{{ $product->name }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $product->category->name }}</td>
        <td class="px-4 py-3 text-slate-300">{{ $product->unit->name }}</td>
        <td class="px-4 py-3 text-right font-mono text-slate-100">Rp
            {{ number_format($product->price, 0, ',', '.') }}</td>
        <td class="px-4 py-3 text-center">
            <span
                class="{{ $product->stok < 10 ? 'bg-red-400/10 text-red-400' : 'bg-green-400/10 text-green-400' }} rounded px-2 py-0.5 text-xs font-medium">
                {{ $product->stok }}
            </span>
        </td>
        <td class="space-x-2 px-4 py-3 text-right">
            <button onclick="editProduct('{{ $product->id }}')"
                class="text-accent hover:text-cyan-300">Edit</button>
            <button onclick="confirmDeleteProduct({{ $product->id }}, '{{ $product->name }}')"
                class="text-red-400 hover:text-red-300">Hapus</button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-4 py-8 text-center italic text-slate-500">Belum ada data produk.
        </td>
    </tr>
@endforelse
