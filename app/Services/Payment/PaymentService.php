<?php

namespace App\Services\Payment;

use App\Models\Tenant\Order;
use App\Models\Tenant\Payment;
use App\Services\Order\OrderStateService;
use DomainException;

class PaymentService
{
    public function pay(Order $order, string $method): Payment
    {
        if ($order->order_type === 'online' && $method !== 'online') {
            throw new DomainException("Online orders require online payment.");
        }

        if ($order->order_type === 'manual' && $method === 'online') {
            throw new DomainException("Manual orders cannot use gateway.");
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $method,
            'amount' => $order->total,
            'status' => $method === 'online' ? 'pending' : 'paid',
            'paid_at' => $method !== 'online' ? now() : null,
        ]);

        if ($method !== 'online') {
            app(OrderStateService::class)->transition($order, 'paid');
        }

        return $payment;
    }
}

