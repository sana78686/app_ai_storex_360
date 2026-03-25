<?php

namespace App\Services\Payment;

use App\Models\Tenant\Payment;

class PaymentGatewayService
{
    public function initiate(Payment $payment): string
    {
        // Call Stripe or other provider here
        return "https://payment-gateway-url.com/redirect";
    }

    public function confirm(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'paid',
            'metadata' => $payload,
            'paid_at' => now(),
        ]);

        $order = $payment->order;
        app(\App\Services\Order\OrderStateService::class)
            ->transition($order, 'paid');
    }
}

