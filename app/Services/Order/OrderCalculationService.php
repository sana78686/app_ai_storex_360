<?php

namespace App\Services\Order;

use App\Models\Tenant\Order;

class OrderCalculationService
{
    public function recalculate(Order $order): Order
    {
        $subtotal = $order->items()->sum('total');
        $tax = $subtotal * 0.16; // Example 16%
        $discount = $order->discount ?? 0;

        $total = $subtotal + $tax - $discount;

        if ($order->negotiation_status === 'accepted') {
            $total = $order->negotiated_total;
        }

        $order->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
        ]);

        return $order->fresh();
    }
}
