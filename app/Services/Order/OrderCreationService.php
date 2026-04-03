<?php

namespace App\Services\Order;

use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderCreationService
{
    public function createOnline(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            return Order::create([
                'order_number'     => Str::uuid(),
                'order_type'       => 'online',
                'fulfillment_type' => $data['fulfillment_type'],
                'customer_id'      => auth()->id(),
                'subtotal'         => 0,
                'discount'         => 0,
                'tax'              => 0,
                'total'            => 0,
                'checkout_token'   => Str::random(40),
                'expires_at'       => now()->addMinutes(30),
            ]);
        });
    }

    public function createManualWithItems(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $fulfillment = $data['fulfillment_type'] ?? 'pickup';
            if ($fulfillment === 'shipping') {
                $fulfillment = 'delivery';
            }

            $order = Order::create([
                'order_number'     => Str::uuid(),
                'order_type'       => 'manual',
                'fulfillment_type' => in_array($fulfillment, ['delivery', 'pickup'], true) ? $fulfillment : 'pickup',
                'created_by'       => auth()->id(),
                'status'           => 'confirmed',
                'subtotal'         => 0,
                'tax'              => 0,
                'discount'         => 0,
                'total'            => 0,
            ]);

            foreach ($data['items'] as $item) {
                $qty = (int) ($item['qty'] ?? 1);
                $unit = (float) ($item['price'] ?? 0);
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'],
                    'product_name'  => $item['name'] ?? 'Product',
                    'product_sku'   => $item['sku'] ?? null,
                    'unit_price'    => $unit,
                    'quantity'      => $qty,
                    'total'         => round($unit * $qty, 2),
                ]);
            }

            app(OrderCalculationService::class)
                ->recalculate($order);

            return $order->fresh(['items']);
        });

    }
}

