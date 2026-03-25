<?php

namespace App\Services\Checkout;

use App\Models\Tenant\Order;
use App\Services\Order\OrderCreationService;
use App\Services\Order\OrderCalculationService;
use App\Services\Payment\PaymentService;

class CheckoutService
{
    public function start(array $data): Order
    {
        return app(OrderCreationService::class)
            ->createOnline($data);
    }

    public function calculate(Order $order)
    {
        return app(OrderCalculationService::class)
            ->recalculate($order);
    }

    public function pay(Order $order)
    {
        return app(PaymentService::class)
            ->pay($order, 'online');
    }
}
