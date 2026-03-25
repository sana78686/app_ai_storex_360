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

            $order = Order::create([
                'order_number'     => Str::uuid(),
                'order_type'       => 'manual',
                'fulfillment_type' => 'pickup',
                // 'customer_id'      => $data['customer_id'],
                'created_by'       => auth()->id(),
                'status'           => 'confirmed',
                'subtotal'         => 0,
                'tax'              => 0,
                'discount'         => 0,
                'total'            => 0,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'product_sku'  => $item['sku'] ?? null,
                    'price'   => $item['price'],
                    'quantity'     => $item['qty'],
                    'total'        => $item['price'] * $item['qty'],
                ]);
            }

            app(OrderCalculationService::class)
                ->recalculate($order);

            return $order->fresh(['items']);
        });

    }
}

