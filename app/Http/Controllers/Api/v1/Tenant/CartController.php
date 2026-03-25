<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cart;
use App\Models\Tenant\CartItem;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get cart by guest_token (header X-Guest-Token or body guest_token) or by auth customer.
     */
    public function index(Request $request)
    {
        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['items' => [], 'total_price' => 0, 'total_items' => 0]);
        }

        $items = $cart->items()->with(['product', 'product.media'])->get()->map(function (CartItem $item) {
            $product = $item->product;
            $imageUrl = null;
            if ($product && $product->relationLoaded('media') && $product->media->isNotEmpty()) {
                $main = $product->media->where('is_main', true)->first() ?? $product->media->first();
                $imageUrl = $main?->cdn_url;
            }
            return [
                'id' => $item->product_id,
                'cart_item_id' => $item->id,
                'title' => $item->product_name,
                'articleNumber' => $item->product_sku,
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'imageUrl' => $imageUrl ?? null,
            ];
        });

        $totalPrice = $cart->items->sum(fn (CartItem $i) => $i->price * $i->quantity);
        $totalItems = $cart->items->sum('quantity');

        return response()->json([
            'items' => $items,
            'total_price' => round($totalPrice, 2),
            'total_items' => $totalItems,
        ]);
    }

    /**
     * Add item to cart. Body: product_id, quantity (optional, default 1), guest_token (if guest).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = $this->resolveOrCreateCart($request);
        $product = Product::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?? 1);

        $existing = $cart->items()->where('product_id', $product->id)->first();
        if ($existing) {
            $existing->update(['quantity' => $existing->quantity + $quantity]);
        } else {
            $productName = $product->getRawOriginal('name') ?? $product->name ?? 'Product';
            if (!is_string($productName) || $productName === '') {
                $productName = 'Product #' . $product->id;
            }
            $cart->items()->create([
                'product_id' => $product->id,
                'product_name' => $productName,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $this->index($request);
    }

    /**
     * Update cart item quantity. Body: cart_item_id or product_id, quantity, guest_token (if guest).
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'nullable|exists:cart_items,id',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        if ($request->cart_item_id) {
            $item = $cart->items()->find($request->cart_item_id);
        } else {
            $item = $cart->items()->where('product_id', $request->product_id)->first();
        }

        if (!$item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        if ($request->quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $request->quantity]);
        }

        return $this->index($request);
    }

    /**
     * Remove item. Body: cart_item_id or product_id, guest_token (if guest).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'nullable|exists:cart_items,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $cart = $this->resolveCart($request);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        if ($request->cart_item_id) {
            $cart->items()->where('id', $request->cart_item_id)->delete();
        } elseif ($request->product_id) {
            $cart->items()->where('product_id', $request->product_id)->delete();
        } else {
            return response()->json(['message' => 'Provide cart_item_id or product_id'], 400);
        }

        return $this->index($request);
    }

    private function resolveCart(Request $request): ?Cart
    {
        $guestToken = $request->header('X-Guest-Token') ?? $request->input('guest_token');
        if ($guestToken) {
            return Cart::where('guest_token', $guestToken)->with('items')->first();
        }
        if ($request->user('customer')) {
            return Cart::where('customer_id', $request->user('customer')->id)->with('items')->first();
        }
        return null;
    }

    private function resolveOrCreateCart(Request $request): Cart
    {
        $cart = $this->resolveCart($request);
        if ($cart) {
            return $cart;
        }

        $guestToken = $request->header('X-Guest-Token') ?? $request->input('guest_token');
        $customerId = $request->user('customer')?->id ?? null;

        return Cart::create([
            'customer_id' => $customerId,
            'guest_token' => $guestToken ?: null,
        ]);
    }
}
