<?php

namespace App\Services\Order;

use App\Models\Tenant\Order;
use DomainException;

class OrderStateService
{
    public function transition(Order $order, string $newState): Order
    {
        $allowed = $this->allowedTransitions($order->status);

        if (!in_array($newState, $allowed)) {
            throw new DomainException("Invalid state transition");
        }

        $order->update(['status' => $newState]);

        return $order;
    }

    private function allowedTransitions(string $current): array
    {
        return match ($current) {
            'draft' => ['negotiation_pending', 'confirmed', 'cancelled'],
            'confirmed' => ['paid', 'cancelled'],
            'paid' => ['processing'],
            'processing' => ['shipped', 'ready_for_pickup'],
            'shipped' => ['completed'],
            'ready_for_pickup' => ['completed'],
            default => [],
        };
    }
}
