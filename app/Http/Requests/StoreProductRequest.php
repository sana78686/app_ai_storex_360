<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        foreach (['categories', 'variants', 'variant_options', 'tags', 'info_fields' ,'technical_info'] as $field) {
            if ($this->filled($field) && is_string($this->$field)) {
                $this->merge([
                    $field => json_decode($this->$field, true)
                ]);
            }
        }
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'nullable|string',
            'detailed_description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'type' => 'required|in:simple,variant,grouped,digital',
            'track_quantity' => 'boolean',
            'stock' => 'nullable|integer|min:0',

            // Categories
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',

            // Tags
            'tags' => 'nullable|array',
            'tags.*' => 'string',

            // Images
            'images' => 'nullable|array',
            'images.*.file' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
            'images.*.is_main' => 'nullable|boolean',

            // Variant Options
            'variant_options' => 'nullable|array',
            'variant_options.*.name' => 'required_with:variant_options|string',
            'variant_options.*.values' => 'required_with:variant_options|array|min:1',
            'variant_options.*.values.*' => 'string',

            // Variants
            'variants' => 'nullable|array',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric',
            'variants.*.stock' => 'nullable|integer|min:0',

            // Info Fields
            'info_fields' => 'nullable|array',
            'info_fields.*.name' => 'required|string|max:255',
            'info_fields.*.value' => 'nullable|string',

            'technical_info' => 'nullable|array',
            'technical_info.*.name' => 'required|string|max:255',
            'technical_info.*.value' => 'required|string',
        ];
    }



    public function messages()
    {
        return [
            // Name
            'name.required' => 'Product name is required.',
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name cannot be longer than 255 characters.',

            // SKU
            'sku.string' => 'SKU must be a valid string.',
            'sku.unique' => 'SKU must be unique. Another product already uses this SKU.',

            // Description
            'description.string' => 'Description must be text.',

            // Price
            'price.numeric' => 'Price must be a valid number.',

            // Type
            'type.required' => 'Product type is required.',
            'type.in' => 'Product type must be one of: simple, variant, grouped, or digital.',

            // Track quantity
            'track_quantity.boolean' => 'Track quantity must be true or false.',

            // Stock
            'stock.integer' => 'Stock must be an integer value.',
            'stock.min' => 'Stock cannot be less than 0.',

            // Categories
            'categories.array' => 'Categories must be sent as an array.',
            'categories.*.exists' => 'One or more selected categories do not exist.',

            // Tags
            'tags.array' => 'Tags must be sent as an array.',
            'tags.*.string' => 'Each tag must be text.',

            // Images
            // 'images.array' => 'Images must be sent as an array.',
            // 'images.*.url' => 'Each image must have a valid URL.',

            // Variant options
            'variant_options.array' => 'Variant options must be sent as an array.',
            'variant_options.*.name.required_with' => 'Each variant option must have a name.',
            'variant_options.*.name.string' => 'Variant option name must be text.',
            'variant_options.*.values.required_with' => 'Each variant option must have at least one value.',
            'variant_options.*.values.array' => 'Variant option values must be in an array.',
            'variant_options.*.values.min' => 'Each variant option must have at least one value.',

            // Variants
            'variants.array' => 'Variants must be sent as an array.',
            'variants.*.sku.string' => 'Variant SKU must be a valid string.',
            'variants.*.price.numeric' => 'Variant price must be a valid number.',
            'variants.*.stock.integer' => 'Variant stock must be an integer.',
            'variants.*.stock.min' => 'Variant stock cannot be less than 0.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
