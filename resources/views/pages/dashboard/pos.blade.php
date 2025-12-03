@extends('layouts.app_barista')

@section('content')
<div class="row h-100">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-primary-custom">Menu Kopi</h4>
            <input type="text" class="form-control w-50 border-0 shadow-sm" placeholder="Cari menu...">
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card-custom p-3 text-center" style="cursor: pointer;">
                    <div class="mb-2 text-primary-custom"><i class="fas fa-coffee fa-3x"></i></div>
                    <h6 class="fw-bold mb-1">Kopi Susu Aren</h6>
                    <span class="text-accent fw-bold">Rp 18.000</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom p-3 text-center" style="cursor: pointer;">
                    <div class="mb-2 text-primary-custom"><i class="fas fa-mug-hot fa-3x"></i></div>
                    <h6 class="fw-bold mb-1">Americano</h6>
                    <span class="text-accent fw-bold">Rp 15.000</span>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card-custom p-3 text-center" style="cursor: pointer;">
                    <div class="mb-2 text-primary-custom"><i class="fas fa-glass-whiskey fa-3x"></i></div>
                    <h6 class="fw-bold mb-1">Latte Ice</h6>
                    <span class="text-accent fw-bold">Rp 22.000</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom d-flex flex-column" style="height: calc(100vh - 80px); position: sticky; top: 20px;">
            <h5 class="fw-bold mb-3 border-bottom pb-3">Keranjang</h5>
            
            <div class="flex-grow-1 overflow-auto">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="fw-bold">Kopi Susu Aren</div>
                        <small class="text-muted">1 x Rp 18.000</small>
                    </div>
                    <div class="fw-bold">Rp 18.000</div>
                </div>
            </div>

            <div class="mt-auto border-top pt-3">
                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>Total</span>
                    <span>Rp 18.000</span>
                </div>
                <button class="btn btn-warning w-100 fw-bold text-white" style="background: var(--accent); border:none;">
                    BAYAR SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>
@endsection