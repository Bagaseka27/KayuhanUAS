@extends('layouts.app')

@section('title', 'Kasir (POS) - Kayuhan')

@section('content')
    {{-- Dummy Data untuk Tampilan (Nanti diganti variable dari Controller) --}}
    @php
        $products = [
            (object)['id' => 'M01', 'name' => 'Kopi Susu Aren', 'price' => 18000],
            (object)['id' => 'M02', 'name' => 'Americano', 'price' => 15000],
            (object)['id' => 'M03', 'name' => 'Latte Ice', 'price' => 22000],
            (object)['id' => 'M04', 'name' => 'Croissant', 'price' => 18000],
            (object)['id' => 'M05', 'name' => 'Matcha Latte', 'price' => 24000],
            (object)['id' => 'M06', 'name' => 'Cold Brew', 'price' => 20000],
        ];
    @endphp

    <div class="row">
        <!-- BAGIAN KIRI: KATALOG PRODUK -->
        <div class="col-lg-8">
            <!-- Header & Search -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-primary-custom">Point of Sales</h4>
                <div class="input-group w-50">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari menu..." onkeyup="filterMenu(this.value)">
                </div>
            </div>

            <!-- Grid Produk -->
            <div class="row g-3" id="pos-grid">
                @foreach($products as $product)
                <div class="col-md-4 col-6 product-item" data-name="{{ strtolower($product->name) }}">
                    <div class="stat-card pos-card p-3 text-center h-100" onclick="addToCart('{{ $product->id }}', '{{ $product->name }}', {{ $product->price }})">
                        <div class="bg-light rounded p-3 mb-2 text-primary-custom">
                            <i class="fas fa-coffee fa-2x"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-truncate">{{ $product->name }}</h6>
                        <small class="text-accent fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- BAGIAN KANAN: KERANJANG (CART) -->
        <div class="col-lg-4">
            <div class="stat-card cart-container p-3 d-flex flex-column" style="height: calc(100vh - 100px); position: sticky; top: 20px;">
                <h5 class="fw-bold border-bottom pb-2 mb-0">Pesanan Baru</h5>
                
                <!-- List Item (Scrollable) -->
                <div id="cart-items" class="cart-scroll my-2 flex-grow-1 overflow-auto">
                    <div class="text-center text-muted py-5" id="empty-cart-msg">
                        <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i><br>
                        Keranjang Kosong
                    </div>
                    <!-- Item akan masuk sini via JS -->
                </div>

                <!-- Footer Cart (Total & Checkout) -->
                <div class="mt-auto pt-3 border-top bg-white">
                    <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                        <span>Total</span>
                        <span id="cart-total" class="text-primary-custom">Rp 0</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Metode Pembayaran</label>
                        <select class="form-select" id="posPaymentMethod" name="payment_method">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Debit">Debit BCA</option>
                        </select>
                    </div>
                    <button class="btn btn-accent w-100 py-3 fw-bold" onclick="processCheckout()">
                        <i class="fas fa-print me-2"></i> BAYAR SEKARANG
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Simple Cart Logic khusus halaman ini
    let cart = [];

    function addToCart(id, name, price) {
        // Cek cart kosong
        document.getElementById('empty-cart-msg').style.display = 'none';

        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.qty++;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        let total = 0;
        
        // Bersihkan list (kecuali msg kosong)
        container.querySelectorAll('.cart-item').forEach(e => e.remove());

        if(cart.length === 0) {
            document.getElementById('empty-cart-msg').style.display = 'block';
        }

        cart.forEach(item => {
            const itemTotal = item.price * item.qty;
            total += itemTotal;

            const html = `
                <div class="cart-item d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <div style="flex: 1;">
                        <div class="fw-bold small text-primary-custom">${item.name}</div>
                        <small class="text-muted">${item.qty} x Rp ${item.price.toLocaleString()}</small>
                    </div>
                    <div class="fw-bold text-end">
                        Rp ${itemTotal.toLocaleString()}
                        <div style="font-size: 0.7rem; cursor: pointer;" class="text-danger" onclick="removeItem('${item.id}')">Hapus</div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });

        document.getElementById('cart-total').innerText = 'Rp ' + total.toLocaleString();
    }

    function removeItem(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function filterMenu(keyword) {
        keyword = keyword.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(keyword) ? 'block' : 'none';
        });
    }

    function processCheckout() {
        if(cart.length === 0) return alert('Keranjang masih kosong!');
        if(confirm('Proses transaksi ini?')) {
            alert('Transaksi Berhasil! Struk sedang dicetak...');
            cart = [];
            renderCart();
        }
    }
</script>
@endpush