@extends('layouts.app_barista')

@section('title', 'Kasir (POS)')

@section('content')

    {{-- Data Menu Diambil dari MenuController@indexPos --}}
    @php
        if (!isset($menuItems)) { $menuItems = collect([]); } 

        $iconMap = [
            'Coffee' => 'fas fa-coffee',
            'Non-Coffee' => 'fas fa-mug-hot'
        ];
    @endphp

    {{-- START: Debugging Block --}}
    @if (!empty($error))
        <div class="alert alert-warning mb-4">
            <strong>Peringatan Menu:</strong> {{ $error }}
        </div>
    @endif
    {{-- END: Debugging Block --}}

    <div class="row g-4">
        <!-- Bagian Kiri: Daftar Menu -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-primary-custom">Point of Sales</h4>
                <input type="text" id="search-input" class="form-control w-50 border-0 shadow-sm" placeholder="Cari menu...">
            </div>

            <div class="row g-3" id="menu-list">
                @forelse($menuItems as $menu)
                <div class="col-md-4 col-sm-6 menu-item-card" data-name="{{ strtolower($menu->NAMA_PRODUK) }}">
                    <div class="card-custom p-3 text-center add-to-cart h-100 d-flex flex-column justify-content-center align-items-center" 
                         style="cursor: pointer; transition: transform 0.2s;"
                         onmouseover="this.style.transform='scale(1.03)'"
                         onmouseout="this.style.transform='scale(1)'"
                         data-id="{{ $menu->ID_PRODUK }}"
                         data-name="{{ $menu->NAMA_PRODUK }}"
                         data-price="{{ $menu->HARGA_JUAL }}">
                        
                        {{-- === PERBAIKAN TAMPILAN FOTO === --}}
                        {{-- Menambahkan bg-light agar ada frame, height dinaikkan ke 120px --}}
                        <div class="mb-3 d-flex align-items-center justify-content-center bg-white border" 
                             style="height: 120px; width: 100%; overflow: hidden; border-radius: 10px;">
                            @if($menu->FOTO)
                                {{-- Menggunakan object-fit: contain agar foto UTUH menyesuaikan kotak --}}
                                <img src="{{ asset('storage/' . $menu->FOTO) }}" 
                                     alt="{{ $menu->NAMA_PRODUK }}" 
                                     style="width: 100%; height: 100%; object-fit: contain;">
                            @else
                                {{-- Jika tidak ada, tampilkan ikon default --}}
                                <i class="{{ $iconMap[$menu->KATEGORI ?? 'Coffee'] ?? 'fas fa-utensils' }} fa-4x text-primary-custom opacity-50"></i>
                            @endif
                        </div>
                        {{-- ==================================== --}}

                        <h6 class="fw-bold mb-1 text-dark">{{ $menu->NAMA_PRODUK }}</h6>
                        <span class="text-success fw-bold">Rp {{ number_format($menu->HARGA_JUAL, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                    <div class="col-12"><p class="text-center text-muted">Tidak ada menu yang tersedia.</p></div>
                @endforelse
            </div>
        </div>

        <!-- Bagian Kanan: Keranjang (Tetap Sama) -->
        <div class="col-md-4">
            <div class="card-custom d-flex flex-column" style="height: calc(100vh - 40px); position: sticky; top: 20px;">
                <h5 class="fw-bold mb-3 border-bottom pb-3">Keranjang</h5>
                
                <div class="grow overflow-auto" id="cart-items" style="flex-grow: 1;">
                    <p id="empty-cart-message" class="text-muted text-center mt-5">Keranjang Kosong</p>
                </div>

                <div class="mt-auto border-top pt-3">
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span id="cart-total" class="fs-5 text-primary-custom">Rp 0</span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Metode Pembayaran</label>
                        <select id="payment-method" class="form-select">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <button id="checkout-button" class="btn btn-warning w-100 fw-bold text-white py-2" 
                            style="background: #003d2e; border:none;" disabled>
                        BAYAR SEKARANG
                    </button>
                    
                    <div id="loading-spinner" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary-custom" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="checkout-message" class="alert mt-3 d-none p-2 small"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cart = {}; 
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalSpan = document.getElementById('cart-total');
        const checkoutButton = document.getElementById('checkout-button');
        const paymentMethodSelect = document.getElementById('payment-method');
        const emptyCartMessage = document.getElementById('empty-cart-message');
        const loadingSpinner = document.getElementById('loading-spinner');
        const checkoutMessage = document.getElementById('checkout-message');
        const searchInput = document.getElementById('search-input');
        const menuItemsCards = document.querySelectorAll('.menu-item-card');
        
        const ROUTE_STORE = '{{ route('transaksi.store') }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        function updateCartUI() {
            let total = 0;
            cartItemsContainer.innerHTML = '';
            let itemCount = 0;

            for (const id in cart) {
                const item = cart[id];
                const itemTotal = item.price * item.qty;
                total += itemTotal;
                itemCount++;

                const itemHtml = `
                    <div class="d-flex justify-content-between mb-3 align-items-center cart-row border-bottom pb-2">
                        <div>
                            <div class="fw-bold text-dark small">${item.name}</div>
                            <small class="text-muted">${item.qty} x Rp ${formatRupiah(item.price)}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-danger me-2 remove-item px-2" data-id="${id}"><i class="fas fa-minus"></i></button>
                            <span class="fw-bold me-2 small">Rp ${formatRupiah(itemTotal)}</span>
                            <button class="btn btn-sm btn-outline-success add-item px-2" data-id="${id}"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                `;
                cartItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            }

            emptyCartMessage.classList.toggle('d-none', itemCount > 0);
            cartTotalSpan.textContent = 'Rp ' + formatRupiah(total);
            checkoutButton.disabled = total === 0; 
            checkoutButton.setAttribute('data-total', total);
        }

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            menuItemsCards.forEach(card => {
                const itemName = card.dataset.name;
                card.style.display = itemName.includes(searchTerm) ? '' : 'none';
            });
        });

        document.getElementById('menu-list').addEventListener('click', function(e) {
            const itemCard = e.target.closest('.add-to-cart');
            if (itemCard) {
                const id = itemCard.dataset.id;
                const name = itemCard.dataset.name;
                const price = parseInt(itemCard.dataset.price);

                if (cart[id]) {
                    cart[id].qty++;
                } else {
                    cart[id] = { name, price, qty: 1 };
                }
                updateCartUI();
            }
        });

        cartItemsContainer.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;
            
            const id = target.dataset.id;
            if (!cart[id]) return;

            if (target.classList.contains('add-item')) {
                cart[id].qty++;
            } else if (target.classList.contains('remove-item')) {
                cart[id].qty--;
                if (cart[id].qty <= 0) {
                    delete cart[id];
                }
            }
            updateCartUI();
        });

        checkoutButton.addEventListener('click', async function() {
            this.disabled = true;
            loadingSpinner.classList.remove('d-none');
            checkoutMessage.classList.add('d-none');

            const payload = {
                total_bayar: parseInt(this.dataset.total),
                metode: paymentMethodSelect.value,
                items: Object.keys(cart).map(id => ({ id_produk: id, jml_item: cart[id].qty })),
                _token: CSRF_TOKEN 
            };

            try {
                const response = await fetch(ROUTE_STORE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();

                if (response.ok && result.success) {
                    checkoutMessage.className = 'alert alert-success mt-3';
                    checkoutMessage.textContent = `✅ Berhasil! Mengarahkan...`;
                    checkoutMessage.classList.remove('d-none');
                    if (result.redirect_url) window.location.href = result.redirect_url; 
                    else {
                        for (let k in cart) delete cart[k];
                        updateCartUI();
                    }
                } else {
                    throw new Error(result.message || 'Transaksi gagal');
                }
            } catch (error) {
                checkoutMessage.className = 'alert alert-danger mt-3';
                checkoutMessage.textContent = `❌ ${error.message}`;
                checkoutMessage.classList.remove('d-none');
            } finally {
                loadingSpinner.classList.add('d-none');
                if(!checkoutMessage.classList.contains('alert-success')) this.disabled = false;
            }
        });

        updateCartUI();
    });
</script>
@endpush