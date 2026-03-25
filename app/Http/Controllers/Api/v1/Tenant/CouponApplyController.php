<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Coupon;
use Illuminate\Http\Request;

class CouponApplyController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'user_id' => 'required',
            'cart_total' => 'required|numeric'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon']);
        }

        if ($coupon->min_order_amount && $request->cart_total < $coupon->min_order_amount) {
            return response()->json(['success' => false, 'message' => "Minimum order amount not met"]);
        }

        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = $request->cart_total * ($coupon->discount_amount / 100);
        }

        if ($coupon->type === 'fixed') {
            $discount = $coupon->discount_amount;
        }

        return response()->json([
            'success' => true,
            'discount' => $discount,
            'final_total' => max($request->cart_total - $discount, 0),
            'coupon' => $coupon
        ]);
    }
}
