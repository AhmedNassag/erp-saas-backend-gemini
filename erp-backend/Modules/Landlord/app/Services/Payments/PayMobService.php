<?php

namespace Modules\Landlord\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Landlord\Models\Tenant;
use Modules\Landlord\Models\Payment;

class PayMobService
{
    private string $baseUrl;
    private string $apiKey;
    private string $integrationId;
    private string $iframeId;
    private string $hmacSecret;
    private string $currency;

    public function __construct()
    {
        $this->baseUrl       = config('paymob.base_url');
        $this->apiKey        = config('paymob.api_key');
        $this->integrationId = config('paymob.integration_id');
        $this->iframeId      = config('paymob.iframe_id');
        $this->hmacSecret    = config('paymob.hmac_secret');
        $this->currency      = config('paymob.currency');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->integrationId) && !empty($this->iframeId);
    }

    public function authenticate(): string
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey,
        ]);

        $body = $response->throw()->json();
        Log::info('PayMob Auth Response', ['response' => $body]);

        return $body['token'];
    }

    public function createOrder(string $authToken, int $amountCents, array $items = []): int
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token'     => $authToken,
            'amount_cents'   => $amountCents,
            'currency'       => $this->currency,
            'merchant_id'    => (int) config('paymob.merchant_id'),
            'items'          => $items,
        ]);

        $body = $response->throw()->json();
        Log::info('PayMob Order Response', ['response' => $body]);

        return $body['id'];
    }

    public function getPaymentKey(string $authToken, int $amountCents, int $orderId, array $billingData): string
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payments/payment_keys", [
            'auth_token'     => $authToken,
            'amount_cents'   => $amountCents,
            'currency'       => $this->currency,
            'order_id'       => $orderId,
            'integration_id' => (int) $this->integrationId,
            'billing_data'   => $billingData,
        ]);

        $body = $response->throw()->json();
        Log::info('PayMob PaymentKey Response', ['response' => $body]);

        return $body['token'];
    }

    public function getIframeUrl(string $paymentKey): string
    {
        return "{$this->baseUrl}/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";
    }

    public function verifyHmac(array $data): bool
    {
        if (!$this->hmacSecret) {
            return false;
        }

        $fields = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];

        $concatenated = '';
        foreach ($fields as $field) {
            $value = $data[$field] ?? '';
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $concatenated .= $value;
        }

        $calculated = hash('sha512', $concatenated);

        Log::info('PayMob HMAC Check', [
            'calculated' => $calculated,
            'received'   => $data['hmac'] ?? 'none',
            'match'      => $calculated === ($data['hmac'] ?? ''),
        ]);

        return $calculated === ($data['hmac'] ?? '');
    }

    public function initPayment(Tenant $tenant, int $packageId, float $packagePrice): Payment
    {
        $amountCents = (int) round($packagePrice * 100);

        $payment = Payment::create([
            'tenant_id'   => $tenant->id,
            'package_id'  => $packageId,
            'amount_cents' => $amountCents,
            'currency'    => $this->currency,
            'status'      => 'pending',
        ]);

        $billingData = [
            'apartment'           => 'N/A',
            'email'               => $tenant->name . '@temp.com',
            'floor'               => 'N/A',
            'first_name'          => $tenant->name,
            'street'              => 'N/A',
            'building'            => 'N/A',
            'phone_number'        => 'N/A',
            'shipping_method'     => 'PKG',
            'postal_code'         => 'N/A',
            'city'                => 'N/A',
            'country'             => 'EG',
            'last_name'           => 'Tenant',
            'state'               => 'N/A',
        ];

        try {
            $authToken  = $this->authenticate();
            $orderId    = $this->createOrder($authToken, $amountCents, [
                ['name' => "Package #{$packageId}", 'amount_cents' => $amountCents, 'quantity' => 1, 'description' => "ERP Subscription - Package {$packageId}"],
            ]);
            $paymentKey = $this->getPaymentKey($authToken, $amountCents, $orderId, $billingData);

            $payment->update([
                'paymob_order_id'     => (string) $orderId,
                'paymob_payment_key'  => $paymentKey,
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('PayMob initPayment failed', ['error' => $e->getMessage()]);
            $payment->update(['status' => 'failed', 'paymob_response' => $e->getMessage()]);
            throw $e;
        }
    }

    public function processCallback(array $data): ?Payment
    {
        $transactionId = $data['id'] ?? null;
        $orderId       = $data['order'] ?? null;
        $success       = ($data['success'] ?? false) === true;
        $amountCents   = $data['amount_cents'] ?? 0;

        if (!$transactionId || !$orderId) {
            Log::warning('PayMob callback missing transaction_id or order', ['data' => $data]);
            return null;
        }

        $payment = Payment::where('paymob_order_id', (string) $orderId)->first();
        if (!$payment) {
            Log::warning('PayMob callback: no payment found for order', ['order_id' => $orderId]);
            return null;
        }

        $payment->update([
            'paymob_transaction_id' => (string) $transactionId,
            'paymob_response'      => json_encode($data),
            'status'               => $success ? 'paid' : 'failed',
        ]);

        Log::info('PayMob payment updated', [
            'payment_id' => $payment->id,
            'status'     => $payment->status,
            'amount'     => $amountCents,
        ]);

        return $payment;
    }
}
