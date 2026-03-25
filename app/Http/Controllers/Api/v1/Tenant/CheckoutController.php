<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cart;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Services\Checkout\CheckoutService;
use App\Services\Negotiation\NegotiationService;
use App\Services\Order\OrderCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Guest/authenticated checkout: create order from cart or from request items.
     * Saves to orders and order_items tables using schema from migrations.
     */
    public function placeOrder(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'required|email',
                'phone' => 'nullable|string|max:50',
                'street' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'country' => 'nullable|string|max:255',
                'guest_token' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        }

        $cart = $this->resolveCart($request);
        $cart?->load('items');
        $itemsFromCart = $cart && $cart->items->isNotEmpty() ? $cart->items : null;
        $itemsFromRequest = $request->input('items', []);

        $orderRows = [];
        if ($itemsFromCart) {
            foreach ($itemsFromCart as $item) {
                $price = (float) $item->price;
                $qty = (int) $item->quantity;
                $orderRows[] = [
                    'product_id' => $item->product_id,
                    'product_name' => (string) ($item->product_name ?: 'Product'),
                    'product_sku' => $item->product_sku,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'total' => round($price * $qty, 2),
                ];
            }
        } else {
            foreach ($itemsFromRequest as $item) {
                $productId = isset($item['product_id']) ? (int) $item['product_id'] : null;
                $price = (float) ($item['price'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 1);
                $orderRows[] = [
                    'product_id' => $productId,
                    'product_name' => (string) ($item['product_name'] ?? 'Product'),
                    'product_sku' => $item['product_sku'] ?? null,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'total' => round($price * $qty, 2),
                ];
            }
        }

        if (empty($orderRows)) {
            return response()->json([
                'success' => false,
                'message' => 'No items to order. Add products to cart and try again.',
            ], 400);
        }

        try {
            return DB::transaction(function () use ($request, $cart, $orderRows) {
                $customer = $this->resolveOrCreateCustomer($request);
                $now = now();
                $orderNumber = (string) Str::uuid();

                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'order_type' => 'online',
                    'fulfillment_type' => 'delivery',
                    'customer_id' => $customer->id,
                    'created_by' => null,
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'negotiated_total' => null,
                    'negotiation_status' => 'none',
                    'status' => 'confirmed',
                    'currency' => 'EUR',
                    'checkout_token' => null,
                    'expires_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($orderRows as $row) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_sku' => $row['product_sku'],
                        'unit_price' => $row['unit_price'],
                        'quantity' => $row['quantity'],
                        'total' => $row['total'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $order = Order::find($orderId);
                if ($order) {
                    app(OrderCalculationService::class)->recalculate($order);
                }

                if ($cart) {
                    $cart->items()->delete();
                }

                $response = [
                    'success' => true,
                    'order_number' => $orderNumber,
                    'order_id' => $orderId,
                ];
                if ($customer->type === 'guest') {
                    $response['set_password_token'] = null;
                }

                return response()->json($response);
            });
        } catch (Throwable $e) {
            Log::error('Checkout placeOrder failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Could not save order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set password for guest customer (after checkout). Optional; frontend may call with token from email later.
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'set_password_token' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password set. You can now log in.',
        ]);
    }

    private function resolveCart(Request $request): ?Cart
    {
        $guestToken = $request->header('X-Guest-Token') ?: $request->input('guest_token');
        if ($guestToken) {
            $cart = Cart::where('guest_token', $guestToken)->with('items')->first();
            if ($cart) {
                return $cart;
            }
        }
        if ($request->user('customer')) {
            return Cart::where('customer_id', $request->user('customer')->id)->with('items')->first();
        }
        return null;
    }

    private function resolveOrCreateCustomer(Request $request): Customer
    {
        if ($request->user('customer')) {
            return $request->user('customer');
        }

        $email = $request->input('email');
        $existing = Customer::where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        return Customer::create([
            'email' => $email,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'type' => 'guest',
        ]);
    }

    public function start(Request $request)
    {
        $request->validate([
            'fulfillment_type' => 'required|in:delivery,pickup',
        ]);

        $order = app(CheckoutService::class)
            ->start($request->only('fulfillment_type'));

        return response()->json([
            'message' => 'Checkout started',
            'data' => $order
        ]);
    }

    public function negotiate(Request $request, Order $order)
    {
        $request->validate([
            'offer' => 'required|numeric|min:1'
        ]);

        $neg = app(NegotiationService::class)
            ->submitOffer($order, $request->offer);

        return response()->json([
            'message' => 'Offer submitted',
            'data' => $neg
        ]);
    }

    public function pay(Order $order)
    {
        $payment = app(CheckoutService::class)
            ->pay($order);

        return response()->json([
            'message' => 'Payment initiated',
            'data' => $payment
        ]);
    }
}
