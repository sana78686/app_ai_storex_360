<?php

namespace App\Services\Order;

use App\Models\Tenant\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /*
    |--------------------------------------------------------------------------
    | LIST ORDERS (Advanced Filtering + Sorting + Pagination)
    |--------------------------------------------------------------------------
    */
    public function list(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query = Order::query()
            ->with(['customer'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($sub) use ($search) {
                    $sub->where('id', 'like', "%{$search}%")
                        ->orWhere('order_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                          ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->payment_status, fn ($q) =>
                $q->where('payment_status', $request->payment_status)
            )
            ->when($request->type, fn ($q) =>
                $q->where('type', $request->type) // online / manual
            )
            ->when($request->from_date, fn ($q) =>
                $q->whereDate('created_at', '>=', $request->from_date)
            )
            ->when($request->to_date, fn ($q) =>
                $q->whereDate('created_at', '<=', $request->to_date)
            );

        // Sorting protection (prevent SQL injection)
        $allowedSorts = ['id', 'created_at', 'total_amount', 'status'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $order = Order::create([
                'customer_id'       => $data['customer_id'],
                'type'              => $data['type'], // online / manual
                'delivery_type'     => $data['delivery_type'] ?? null, // delivery / pickup
                'status'            => 'pending',
                'payment_status'    => $data['payment_status'] ?? 'pending',
                'total_amount'      => $data['total_amount'],
                'notes'             => $data['notes'] ?? null,
            ]);

            // If you have order items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity'   => $item['quantity'],
                        'price'      => $item['price'],
                        'total'      => $item['quantity'] * $item['price'],
                    ]);
                }
            }

            return $order->load(['customer', 'items']);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ORDER STATUS
    |--------------------------------------------------------------------------
    */
    public function updateStatus(int $orderId, string $status)
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => $status
        ]);

        return $order;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PAYMENT STATUS
    |--------------------------------------------------------------------------
    */
    public function updatePaymentStatus(int $orderId, string $paymentStatus)
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'payment_status' => $paymentStatus
        ]);

        return $order;
    }

    /*
    |--------------------------------------------------------------------------
    | BULK FULFILL ORDERS
    |--------------------------------------------------------------------------
    */
    public function fulfill(array $data)
    {
        return DB::transaction(function () use ($data) {

            $orders = Order::whereIn('id', $data['order_ids'])->get();

            foreach ($orders as $order) {
                $order->update([
                    'fulfillment_status' => 'fulfilled',
                    'carrier'            => $data['carrier'],
                    'tracking_number'    => $data['tracking_number'] ?? null,
                    'fulfilled_at'       => now(),
                ]);
            }

            return $orders;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ORDER
    |--------------------------------------------------------------------------
    */
    public function delete(int $orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | ORDER DASHBOARD STATS (For SaaS Dashboard)
    |--------------------------------------------------------------------------
    */
    public function stats()
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('payment_status', 'paid')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];
    }
}
