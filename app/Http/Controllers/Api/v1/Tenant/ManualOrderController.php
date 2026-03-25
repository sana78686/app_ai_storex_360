<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Order;
use App\Services\Order\OrderCreationService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;

class ManualOrderController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $order = app(OrderCreationService::class)
            ->createManualWithItems($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Manual order created successfully',
            'data'    => $order
        ]);
    }

    public function pay(Request $request, Order $order)
    {
        $request->validate([
            'method' => 'required|in:cash,card,bank_transfer'
        ]);

        $payment = app(PaymentService::class)
            ->pay($order, $request->method);

        return response()->json([
            'message' => 'Payment successful',
            'data' => $payment
        ]);
    }
}
