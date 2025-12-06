@extends('layouts.app_barista')

@section('title', 'Kasir (POS)')

@section('content')

    {{-- Data Menu Diambil dari MenuController@indexPos --}}
    @php
        // Pastikan $menuItems sudah diisi dari Controller
        if (!isset($menuItems)) { $menuItems = collect([]); } 

<<<<<<< HEAD
        // Definisikan ikon default jika tidak ada kolom icon di DB
        $iconMap = [
            'Coffee' => 'fas fa-coffee',
            'Non-Coffee' => 'fas fa-mug-hot'
        ];
    @endphp

    <div class="row g-4">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-primary-custom">Point of Sales</h4>
                <input type="text" id="search-input" class="form-control w-50 border-0 shadow-sm" placeholder="Cari menu...">
            </div>

            <div class="row g-3" id="menu-list">
                {{-- LOOP DATA MENU DARI DATABASE --}}
                @forelse($menuItems as $menu)
                <div class="col-md-4 menu-item-card" data-name="{{ strtolower($menu->NAMA_PRODUK) }}">
                    <div class="card-custom p-3 text-center add-to-cart" style="cursor: pointer;"
                         data-id="{{ $menu->ID_PRODUK }}"
                         data-name="{{ $menu->NAMA_PRODUK }}"
                         data-price="{{ $menu->HARGA_JUAL }}">
                        
                        {{-- Menggunakan ikon berdasarkan kategori (jika ada) --}}
                        <div class="mb-2 text-primary-custom">
                            <i class="{{ $iconMap[$menu->CATEGORY ?? 'Coffee'] ?? 'fas fa-utensils' }} fa-3x"></i>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $menu->NAMA_PRODUK }}</h6>
                        <span class="text-accent fw-bold">Rp {{ number_format($menu->HARGA_JUAL, 0, ',', '.') }}</span>
=======
    <div class="col-md-4">
        <div class="card-custom d-flex flex-column" style="height: calc(100vh - 80px); position: sticky; top: 20px;">
            <h5 class="fw-bold mb-3 border-bottom pb-3">Keranjang</h5>
            
            <div class="grow overflow-auto">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="fw-bold">Kopi Susu Aren</div>
                        <small class="text-muted">1 x Rp 18.000</small>
>>>>>>> cd20386144a8993ea144b64acc0413311973cfa0
                    </div>
                </div>
                @empty
                    <div class="col-12"><p class="text-center text-muted">Tidak ada menu yang tersedia.</p></div>
                @endforelse
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom d-flex flex-column" style="height: calc(100vh - 80px); position: sticky; top: 20px;">
                <h5 class="fw-bold mb-3 border-bottom pb-3">Keranjang</h5>
                
                <div class="flex-grow-1 overflow-auto" id="cart-items">
                    <p id="empty-cart-message" class="text-muted text-center mt-5">Keranjang Kosong</p>
                </div>

                <div class="mt-auto border-top pt-3">
                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Total</span>
                        <span id="cart-total" class="fs-5 text-primary-custom">Rp 0</span>
                    </div>
                    
                    {{-- Metode Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Metode Pembayaran</label>
                        <select id="payment-method" class="form-select">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <button id="checkout-button" class="btn btn-warning w-100 fw-bold text-white" 
                            style="background: var(--accent); border:none;" disabled>
                        BAYAR SEKARANG
                    </button>
                    <div id="loading-spinner" class="text-center mt-3 d-none">
                        <div class="spinner-border text-primary-custom" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="checkout-message" class="alert mt-3 d-none"></div>
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
        
        // Mengaktifkan kembali Named Route dan CSRF
        const ROUTE_STORE = '{{ route('transaksi.store') }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        // --- FUNGSI UTAMA CART ---

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
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">${item.qty} x Rp ${formatRupiah(item.price)}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-danger me-1 remove-item" data-id="${id}"><i class="fas fa-minus"></i></button>
                            <span class="fw-bold me-1">Rp ${formatRupiah(itemTotal)}</span>
                            <button class="btn btn-sm btn-outline-success add-item" data-id="${id}"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                `;
                cartItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            }

            // Tampilkan atau sembunyikan pesan keranjang kosong
            emptyCartMessage.classList.toggle('d-none', itemCount > 0);
            
            cartTotalSpan.textContent = 'Rp ' + formatRupiah(total);
            // Tombol diaktifkan jika total > 0
            checkoutButton.disabled = total === 0; 
            checkoutButton.setAttribute('data-total', total);
        }

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // --- FUNGSI PENCARIAN MENU ---
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            menuItemsCards.forEach(card => {
                const itemName = card.dataset.name;
                
                if (itemName.includes(searchTerm)) {
                    card.style.display = ''; // Tampilkan
                } else {
                    card.style.display = 'none'; // Sembunyikan
                }
            });
        });


        // --- EVENT HANDLERS KERANJANG ---

        // 1. Menambah Item dari Menu (KLIK)
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

        // 2. Mengelola Quantity di Keranjang
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
                    delete cart[id]; // Hapus jika kuantitas 0
                }
            }
            updateCartUI();
        });

        // 3. Proses Checkout (AJAX AKTIF KEMBALI)
        checkoutButton.addEventListener('click', async function() {
            checkoutButton.disabled = true;
            loadingSpinner.classList.remove('d-none');
            checkoutMessage.classList.add('d-none');

            // Kumpulkan data untuk Controller
            const totalBayar = parseInt(this.dataset.total);
            const metodePembayaran = paymentMethodSelect.value;
            
            // Format array items sesuai validasi Controller (id_produk, jml_item)
            const items = Object.keys(cart).map(id => ({
                id_produk: id,
                jml_item: cart[id].qty
            }));

            const payload = {
                total_bayar: totalBayar,
                metode: metodePembayaran,
                items: items,
                _token: CSRF_TOKEN 
            };

            try {
                const response = await fetch(ROUTE_STORE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN 
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Transaksi sukses
                    checkoutMessage.classList.remove('alert-danger');
                    checkoutMessage.classList.add('alert-success');
                    // Mengubah pesan untuk menunjukkan pengalihan
                    checkoutMessage.textContent = `✅ Transaksi ${result.id} berhasil dicatat! Mengarahkan...`;
                    
                    // --- PENGALIHAN KE RIWAYAT BARISTA ---
                    if (result.redirect_url) {
                        // Redirect ke halaman Riwayat Barista setelah sukses
                        window.location.href = result.redirect_url; 
                    } else {
                        // Fallback (seharusnya tidak terjadi jika Controller benar)
                        Object.keys(cart).forEach(key => delete cart[key]);
                        updateCartUI(); 
                    }
                    // --- AKHIR PENGALIHAN ---

                } else {
                    // Transaksi gagal (misal validasi atau error DB)
                    checkoutMessage.classList.remove('alert-success');
                    checkoutMessage.classList.add('alert-danger');
                    checkoutMessage.textContent = `❌ Transaksi Gagal. ${result.message || 'Terjadi kesalahan pada server. (Cek konsol untuk detail error)'}`;
                }

            } catch (error) {
                checkoutMessage.classList.remove('alert-success');
                checkoutMessage.classList.add('alert-danger');
                checkoutMessage.textContent = `❌ Error Jaringan: ${error.message}`;
            } finally {
                loadingSpinner.classList.add('d-none');
                checkoutButton.disabled = false;
                checkoutMessage.classList.remove('d-none');
            }
        });


        // Inisialisasi awal
        updateCartUI();
    });
</script>
@endpush