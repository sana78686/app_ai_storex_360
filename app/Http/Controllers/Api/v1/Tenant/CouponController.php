<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::paginate(20);
        return response()->json($coupons);
    }



   public function store(Request $request)
{
    $request->validate([
        'code' => 'required|unique:coupons,code',
        'type' => 'required|in:percentage,fixed,free_shipping',
        'discount_amount' => 'nullable|numeric|min:0',
        'min_order_amount' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:1',
        'usage_per_customer' => 'nullable|integer|min:1',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'boolean',
        'applies_to' => 'array',  // 👈 validate categories
        'applies_to.*' => 'exists:categories,id',
    ]);

    $coupon = Coupon::create($request->except('applies_to'));

    // 🔥 Attach categories to pivot
    if ($request->has('applies_to')) {
        $coupon->categories()->sync($request->applies_to);
    }

    return response()->json([
        'message' => 'Coupon created successfully!',
        'data' => $coupon->load('categories'),
    ], 201);
}




    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,

        'type' => 'required|in:percentage,fixed,free_shipping',
        'discount_amount' => 'nullable|numeric|min:0',
        'min_order_amount' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:1',
        'usage_per_customer' => 'nullable|integer|min:1',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'boolean',
        'applies_to' => 'array',  // 👈 validate categories
        'applies_to.*' => 'exists:categories,id',


        ]);

        $coupon->update($request->all());
  if ($request->has('applies_to')) {
        $coupon->categories()->sync($request->applies_to);
    }
        return response()->json([
            'message' => 'Coupon updated successfully!',
            'data' => $coupon->load('categories')
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted!']);
    }
    public function show(Coupon $coupon)
{
    return response()->json([
        'data' => $coupon->load('categories')
    ]);
}

}
