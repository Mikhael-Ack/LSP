<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Billing;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Generate Midtrans Snap Token for a Billing instance.
     * Supports both real Midtrans Sandbox API and interactive Sandbox Simulator fallback.
     */
    public static function createSnapToken(Billing $billing)
    {
        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);

        $endpoint = $isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $orderId = $billing->no_tagihan . '-' . substr(uniqid(), -4);

        $items = [];
        if ($billing->pesanan && $billing->pesanan->detail) {
            foreach ($billing->pesanan->detail as $detail) {
                $items[] = [
                    'id' => (string) $detail->produk_id,
                    'price' => (int) $detail->harga_satuan,
                    'quantity' => (int) $detail->jumlah,
                    'name' => substr($detail->produk->nama_produk ?? 'Menu Resto', 0, 50),
                ];
            }
        }

        // Add 10% PB1 tax item
        if ($billing->pajak > 0) {
            $items[] = [
                'id' => 'TAX-PB1',
                'price' => (int) $billing->pajak,
                'quantity' => 1,
                'name' => 'Pajak Restoran PB1 (10%)',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $billing->grand_total,
            ],
            'customer_details' => [
                'first_name' => 'Pelanggan ' . ($billing->pesanan->no_meja ?? 'Meja Resto'),
                'email' => 'kasir@dapurinaaina.com',
                'phone' => '081234567890',
            ],
            'item_details' => $items,
        ];

        try {
            $response = Http::timeout(5)
                ->withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['token'])) {
                    return [
                        'success' => true,
                        'token' => $data['token'],
                        'redirect_url' => $data['redirect_url'] ?? null,
                        'order_id' => $orderId,
                        'mode' => 'midtrans_sandbox_live',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::info('Midtrans live call failed, falling back to simulator: ' . $e->getMessage());
        }

        // Seamless Midtrans Sandbox Simulator mode
        $simToken = 'SNAP-SANDBOX-' . strtoupper(substr(md5($orderId), 0, 16));
        return [
            'success' => true,
            'token' => $simToken,
            'redirect_url' => 'https://simulator.sandbox.midtrans.com/qris/index',
            'order_id' => $orderId,
            'mode' => 'simulator',
        ];
    }

    /**
     * Generate BCA or other Bank Virtual Account for Midtrans Sandbox Simulator.
     */
    public static function createVaCharge(Billing $billing, $bank = 'bca')
    {
        $serverKey = config('midtrans.server_key');
        $isProduction = config('midtrans.is_production', false);
        $bank = strtolower($bank);
        $orderId = $billing->no_tagihan;

        // Default Midtrans Sandbox Virtual Account prefixes
        $prefixes = [
            'bca' => '91012',
            'bni' => '988',
            'bri' => '10777',
            'permata' => '8778',
        ];
        $prefix = $prefixes[$bank] ?? '91012';
        $defaultVa = $prefix . str_pad($billing->id, 8, '0', STR_PAD_LEFT);

        $isRealKey = $serverKey && !str_contains($serverKey, 'DemoResto') && str_starts_with($serverKey, 'SB-Mid-server-');

        if ($isRealKey) {
            $endpoint = $isProduction 
                ? 'https://api.midtrans.com/v2/charge' 
                : 'https://api.sandbox.midtrans.com/v2/charge';

            try {
                $chargeOrderId = $orderId . '-' . substr(uniqid(), -4);
                $payload = [
                    'payment_type' => 'bank_transfer',
                    'transaction_details' => [
                        'order_id' => $chargeOrderId,
                        'gross_amount' => (int) $billing->grand_total,
                    ],
                    'bank_transfer' => [
                        'bank' => $bank,
                    ],
                    'customer_details' => [
                        'first_name' => 'Pelanggan Meja ' . ($billing->pesanan->no_meja ?? '-'),
                        'email' => 'kasir@dapurinaaina.com',
                    ],
                ];

                $response = Http::timeout(4)
                    ->withBasicAuth($serverKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post($endpoint, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $liveVa = null;
                    if (!empty($data['va_numbers'][0]['va_number'])) {
                        $liveVa = $data['va_numbers'][0]['va_number'];
                    } elseif (!empty($data['permata_va_number'])) {
                        $liveVa = $data['permata_va_number'];
                    }

                    if ($liveVa) {
                        return [
                            'status' => 'success',
                            'order_id' => $data['order_id'] ?? $chargeOrderId,
                            'va_number' => $liveVa,
                            'bank' => strtoupper($bank),
                            'gross_amount' => (int) $billing->grand_total,
                            'mode' => 'midtrans_live_sandbox',
                            'simulator_url' => 'https://simulator.sandbox.midtrans.com/' . $bank . '/va/index',
                            'message' => 'Terdaftar di Midtrans Sandbox Server',
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::info('Midtrans live charge exception: ' . $e->getMessage());
            }
        }

        return [
            'status' => 'success',
            'order_id' => $orderId,
            'va_number' => $defaultVa,
            'bank' => strtoupper($bank),
            'gross_amount' => (int) $billing->grand_total,
            'mode' => 'sandbox_simulator',
            'simulator_url' => 'https://simulator.sandbox.midtrans.com/' . $bank . '/va/index',
            'message' => 'Mode Sandbox Simulator',
        ];
    }
}
