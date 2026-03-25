<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Product;
use App\Models\Tenant\ProductVariant;
use App\Models\Tenant\VariantValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    /**
     * Store a new variant for a product
     */
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'values' => 'required|array',
            'values.*.option_id' => 'required|exists:variant_options,id',
            'values.*.value' => 'required|string|max:255', // Accept value directly
            'values.*.value_id' => 'nullable|exists:variant_values,id' // Optional existing value
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the variant
        $variant = $product->variants()->create([
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_default' => false,
        ]);

        // Process variant values
        $variantValues = [];
        foreach ($request->values as $valueData) {
            // Use existing value or create new one
            $value = $valueData['value_id']
                ? VariantValue::find($valueData['value_id'])
                : VariantValue::firstOrCreate([
                    'variant_option_id' => $valueData['option_id'],
                    'value' => $valueData['value']
                ]);

            $variantValues[$value->id] = [
                'variant_option_id' => $valueData['option_id']
            ];
        }

        // Attach values to variant
        $variant->variantValues()->sync($variantValues);

        return response()->json([
            'variant' => $variant->load('variantValues.option'),
            'message' => 'Variant created successfully'
        ], 201);
    }

    /**
     * Update an existing variant
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $validator = Validator::make($request->all(), [
            'sku' => 'sometimes|required|string|unique:product_variants,sku,'.$variant->id,
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'values' => 'sometimes|array',
            'values.*.option_id' => 'required_with:values|exists:variant_options,id',
            'values.*.value' => 'required_with:values|string|max:255',
            'values.*.value_id' => 'nullable|exists:variant_values,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update variant attributes
        $variant->update($request->only(['sku', 'price', 'stock']));

        // Update variant values if provided
        if ($request->has('values')) {
            $variantValues = [];
            foreach ($request->values as $valueData) {
                $value = $valueData['value_id']
                    ? VariantValue::find($valueData['value_id'])
                    : VariantValue::firstOrCreate([
                        'variant_option_id' => $valueData['option_id'],
                        'value' => $valueData['value']
                    ]);

                $variantValues[$value->id] = [
                    'variant_option_id' => $valueData['option_id']
                ];
            }
            $variant->variantValues()->sync($variantValues);
        }

        return response()->json([
            'variant' => $variant->fresh()->load('variantValues.option'),
            'message' => 'Variant updated successfully'
        ]);
    }

    /**
     * Delete a variant
     */
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return response()->json(null, 204);
    }
}
