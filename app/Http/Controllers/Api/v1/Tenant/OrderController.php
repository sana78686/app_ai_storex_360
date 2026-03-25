<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List orders
     */
    public function index(Request $request)
{
    $orders =  app(OrderService::class)->list($request);

    return response()->json([
        'success' => true,
        'data' => $orders
    ]);
}


    /**
     * Show single order
     */
    public function show($id)
    {
        $order = Order::with(['invoice', 'items'])->findOrFail($id);

        return response()->json([
            'order' => $order,
            'invoice' => $order->invoice ? [
                'id' => $order->invoice->id,
                'number' => $order->invoice->invoice_number,
                'pdf_url' => optional(
                    $order->invoice->media()->where('type', 'invoice')->first()
                )->cdn_url
            ] : null
        ]);
    }



}
