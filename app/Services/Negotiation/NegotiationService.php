<?php

namespace App\Services\Negotiation;

use App\Models\Tenant\Order;
use App\Models\Tenant\OrderNegotiation;
use App\Services\Order\OrderStateService;

class NegotiationService
{
    public function submitOffer(Order $order, float $offer)
    {
        $neg = OrderNegotiation::create([
            'order_id' => $order->id,
            'customer_offer' => $offer,
            'status' => 'pending',
            'expires_at' => now()->addHours(6),
        ]);

        app(OrderStateService::class)
            ->transition($order, 'negotiation_pending');

        return $neg;
    }

    public function accept(Order $order, float $finalPrice)
    {
        $order->update([
            'negotiated_total' => $finalPrice,
            'negotiation_status' => 'accepted',
        ]);

        return app(OrderStateService::class)
            ->transition($order, 'confirmed');
    }
}
