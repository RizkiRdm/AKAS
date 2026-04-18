{{-- Reference: DESIGN.md & ARCHITECTURE.md --}}
@extends('layouts.app')

@section('content')
<div class="flex gap-6 h-[calc(100vh-8rem)]">
    <!-- LEFT: Product Area (60%) -->
    <div class="w-3/5 flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-100">Pilih Produk</h2>
            <div class="relative w-64">
                <input type="text" id="product-search" placeholder="Cari SKU atau nama..." 
                    class="w-full bg-secondary text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20"
                    oninput="filterProducts(this.value)">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto pr-2 grid grid-cols-3 gap-4" id="product-grid">
            @foreach($products as $product)
            <div class="product-card bg-secondary border border-slate-700 rounded-xl p-4 flex flex-col gap-2 hover:border-accent cursor-pointer transition-none"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-sku="{{ $product->sku }}"
                 onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
                <div class="flex justify-between items-start">
                    <span class="text-xs text-slate-400 font-mono">{{ $product->sku }}</span>
                    <span class="px-2 py-0.5 bg-slate-700 text-slate-300 rounded text-[10px]">{{ $product->category->name }}</span>
                </div>
                <h3 class="text-sm font-medium text-slate-100 leading-tight h-10 overflow-hidden">{{ $product->name }}</h3>
                <div class="flex justify-between items-end mt-auto">
                    <span class="text-accent font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="text-xs {{ $product->stok < 10 ? 'text-red-400' : 'text-slate-400' }}">Stok: {{ $product->stok }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT: Cart (40%) -->
    <div class="w-2/5 flex flex-col bg-secondary border border-slate-700 rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex justify-between items-center">
            <h2 class="font-medium text-slate-100">Keranjang</h2>
            <button onclick="clearCart()" class="text-xs text-slate-400 hover:text-red-400">Bersihkan</button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 flex flex-col gap-3" id="cart-items">
            <!-- Empty state -->
            <div id="cart-empty" class="h-full flex flex-col items-center justify-center text-slate-500 gap-2">
                <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span class="text-sm">Keranjang kosong</span>
            </div>
        </div>

        <!-- Summary & Checkout -->
        <div class="p-6 bg-slate-900/50 border-t border-slate-700 flex flex-col gap-4">
            <div class="flex justify-between text-slate-400 text-sm">
                <span>Subtotal</span>
                <span id="summary-subtotal">Rp 0</span>
            </div>
            <div class="flex justify-between text-slate-100 font-bold text-xl">
                <span>TOTAL</span>
                <span id="summary-total" class="text-accent">Rp 0</span>
            </div>

            <div class="grid grid-cols-3 gap-2 mt-2">
                <button onclick="showCheckoutModal('cash')" class="flex flex-col items-center gap-1 p-3 bg-slate-800 border border-slate-700 rounded-xl hover:border-accent text-slate-300 hover:text-accent">
                    <span class="text-xs font-medium">TUNAI</span>
                </button>
                <button onclick="showCheckoutModal('qris')" class="flex flex-col items-center gap-1 p-3 bg-slate-800 border border-slate-700 rounded-xl hover:border-accent text-slate-300 hover:text-accent">
                    <span class="text-xs font-medium">QRIS</span>
                </button>
                <button onclick="showCheckoutModal('transfer')" class="flex flex-col items-center gap-1 p-3 bg-slate-800 border border-slate-700 rounded-xl hover:border-accent text-slate-300 hover:text-accent">
                    <span class="text-xs font-medium">TRANSFER</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Checkout -->
<div id="modal-checkout" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50">
    <div class="bg-secondary rounded-2xl w-[400px] shadow-2xl border border-slate-700">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-medium text-slate-100">Konfirmasi Pembayaran</h3>
            <button onclick="closeModal('modal-checkout')" class="text-slate-400 hover:text-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="form-checkout" onsubmit="submitCheckout(event)" class="p-6 flex flex-col gap-4">
            <input type="hidden" id="checkout-method" name="payment_method">
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-slate-400">Metode</span>
                <span id="display-method" class="uppercase font-bold text-accent"></span>
            </div>

            <div class="flex justify-between items-center text-xl font-bold text-slate-100 mb-4">
                <span>Total Bayar</span>
                <span id="display-total"></span>
            </div>

            <div id="payment-ref-container" class="hidden">
                <label class="block text-sm font-medium text-slate-300 mb-1">Referensi / ID Transaksi</label>
                <input type="text" id="payment_ref" name="payment_ref" 
                    class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20 placeholder-slate-500">
            </div>

            <div id="cash-change-container" class="hidden flex flex-col gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Jumlah Uang</label>
                    <input type="number" id="cash-amount" class="w-full bg-dominant text-slate-100 border border-slate-700 rounded-lg px-3 py-2 text-sm focus:border-accent focus:ring-2 focus:ring-accent/20" oninput="calculateChange()">
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-400">Kembalian</span>
                    <span id="cash-change" class="text-green-400 font-bold">Rp 0</span>
                </div>
            </div>

            <button type="submit" id="btn-submit-checkout" class="w-full bg-accent text-slate-900 font-bold py-3 rounded-xl hover:bg-cyan-300 transition-none mt-2">
                KONFIRMASI SELESAI
            </button>
        </form>
    </div>
</div>

<script>
let cart = [];

function filterProducts(query) {
    const cards = document.querySelectorAll('.product-card');
    query = query.toLowerCase();
    
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const sku = card.dataset.sku.toLowerCase();
        if (name.includes(query) || sku.includes(query)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id, name, price, qty: 1 });
    }
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
    }
    renderCart();
}

function clearCart() {
    if (confirm('Bersihkan keranjang?')) {
        cart = [];
        renderCart();
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyState = document.getElementById('cart-empty');
    
    if (cart.length === 0) {
        container.innerHTML = '';
        container.appendChild(emptyState);
        emptyState.classList.remove('hidden');
        document.getElementById('summary-subtotal').innerText = 'Rp 0';
        document.getElementById('summary-total').innerText = 'Rp 0';
        return;
    }

    emptyState.classList.add('hidden');
    container.innerHTML = '';

    let total = 0;
    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        total += subtotal;

        const el = document.createElement('div');
        el.className = 'flex flex-col gap-1 p-3 bg-slate-800/50 rounded-xl border border-slate-700/50';
        el.innerHTML = `
            <div class="flex justify-between items-start">
                <span class="text-sm text-slate-100 font-medium leading-tight">${item.name}</span>
                <button onclick="updateQty(${item.id}, -${item.qty})" class="text-slate-500 hover:text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex justify-between items-center mt-1">
                <div class="flex items-center gap-3 bg-slate-900 rounded-lg px-2 py-1 border border-slate-700">
                    <button onclick="updateQty(${item.id}, -1)" class="text-accent hover:text-cyan-300 font-bold">-</button>
                    <span class="text-xs text-slate-100 min-w-[20px] text-center">${item.qty}</span>
                    <button onclick="updateQty(${item.id}, 1)" class="text-accent hover:text-cyan-300 font-bold">+</button>
                </div>
                <span class="text-sm font-bold text-slate-100">Rp ${subtotal.toLocaleString('id-ID')}</span>
            </div>
        `;
        container.appendChild(el);
    });

    document.getElementById('summary-subtotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('summary-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function showCheckoutModal(method) {
    if (cart.length === 0) return;

    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    document.getElementById('checkout-method').value = method;
    document.getElementById('display-method').innerText = method;
    document.getElementById('display-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
    
    // Reset containers
    document.getElementById('payment-ref-container').classList.add('hidden');
    document.getElementById('cash-change-container').classList.add('hidden');
    document.getElementById('cash-amount').value = '';
    document.getElementById('cash-change').innerText = 'Rp 0';
    document.getElementById('payment_ref').value = '';

    if (method === 'cash') {
        document.getElementById('cash-change-container').classList.remove('hidden');
    } else {
        document.getElementById('payment-ref-container').classList.remove('hidden');
    }

    openModal('modal-checkout');
}

function calculateChange() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const amount = parseInt(document.getElementById('cash-amount').value) || 0;
    const change = amount - total;
    
    const changeEl = document.getElementById('cash-change');
    changeEl.innerText = 'Rp ' + (change > 0 ? change.toLocaleString('id-ID') : '0');
    
    if (change < 0) {
        changeEl.classList.add('text-red-400');
        changeEl.classList.remove('text-green-400');
    } else {
        changeEl.classList.remove('text-red-400');
        changeEl.classList.add('text-green-400');
    }
}

async function submitCheckout(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btn-submit-checkout');
    btn.disabled = true;
    btn.innerText = 'PROSES...';

    const method = document.getElementById('checkout-method').value;
    const ref = document.getElementById('payment_ref').value;
    
    const data = {
        items: cart.map(item => ({ product_id: item.id, qty: item.qty })),
        payment_method: method,
        payment_ref: ref
    };

    try {
        const response = await fetch('/sales', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok) {
            alert('Transaksi berhasil!');
            window.location.reload();
        } else {
            alert('Gagal: ' + (result.message || 'Error tidak diketahui'));
            btn.disabled = false;
            btn.innerText = 'KONFIRMASI SELESAI';
        }
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan network');
        btn.disabled = false;
        btn.innerText = 'KONFIRMASI SELESAI';
    }
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
</script>
@endsection
