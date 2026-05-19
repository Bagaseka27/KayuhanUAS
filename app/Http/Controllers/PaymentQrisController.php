<?php

namespace App\Http\Controllers;

use Xendit\Xendit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentQrisController extends Controller
{
    public function __construct()
    {
        Xendit::setApiKey(config('xendit.api_key'));
        Xendit::setSecretKey(config('xendit.secret_key'));
    }

    /**
     * Generate QRIS untuk pembayaran
     */
    public function generateQr(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'description' => 'nullable|string|max:255',
        ]);

        $referenceId = 'ORDER-' . Str::upper(Str::random(8)) . '-' . time();
        
        try {
            $qrCode = \Xendit\QrCode::create([
                'reference_id' => $referenceId,
                'type' => 'DYNAMIC',
                'currency' => 'IDR',
                'amount' => (int) $validated['amount'],
                'channel_code' => 'QRIS',
                'description' => $validated['description'] ?? 'Pembayaran',
                'expires_at' => date('c', strtotime('+24 hours')),
                'callback_url' => route('payment.webhook'),
            ]);

            return response()->json([
                'success' => true,
                'qr_code' => $qrCode['qr_string'],
                'qr_code_id' => $qrCode['id'],
                'reference_id' => $referenceId,
                'amount' => $validated['amount'],
                'qr_image_url' => "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCode['qr_string']),
            ]);
        } catch (\Exception $e) {
            Log::error('QRIS Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Tampilkan halaman pembayaran QRIS
     */
    public function showPayment(Request $request)
    {
        $amount = $request->get('amount', 50000);
        $referenceId = 'ORDER-' . Str::upper(Str::random(8)) . '-' . time();
        
        try {
            $qrCode = \Xendit\QrCode::create([
                'reference_id' => $referenceId,
                'type' => 'DYNAMIC',
                'currency' => 'IDR',
                'amount' => (int) $amount,
                'channel_code' => 'QRIS',
                'description' => 'Pembayaran Pesanan',
                'expires_at' => date('c', strtotime('+24 hours')),
                'callback_url' => route('payment.webhook'),
            ]);

            return view('qris-payment', [
                'qr_code' => $qrCode['qr_string'],
                'qr_image_url' => "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($qrCode['qr_string']),
                'amount' => $amount,
                'reference_id' => $referenceId,
                'qr_code_id' => $qrCode['id'],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Page Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate QRIS: ' . $e->getMessage());
        }
    }

    /**
     * Check status pembayaran
     */
    public function checkStatus($referenceId)
    {
        try {
            $invoice = \Xendit\Invoice::retrieve($referenceId);
            
            return response()->json([
                'success' => true,
                'status' => $invoice['status'],
                'paid_at' => $invoice['paid_at'] ?? null,
                'payment_method' => $invoice['payment_method'] ?? null,
                'amount' => $invoice['amount'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle webhook dari Xendit (pembayaran berhasil)
     */
    public function webhook(Request $request)
    {
        Log::info('Xendit Webhook Received: ', $request->all());

        // Verifikasi token webhook
        $token = $request->header('X-Callback-Token');
        if ($token !== config('xendit.webhook_token')) {
            Log::warning('Invalid webhook token');
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $data = $request->all();

        // Handle berbagai event
        if ($data['event'] === 'qr_code.charge_succeeded') {
            // PEMBAYARAN BERHASIL
            $referenceId = $data['reference_id'];
            
            // Update database sesuai kebutuhan Anda
            Log::info("Payment successful for reference: {$referenceId}");

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'processed']);
    }
}
