<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Product;
use App\Models\Tenant\ProductVariant;
use App\Models\Tenant\VariantOption;
use App\Models\Tenant\VariantOptionValue;
use App\Models\Tenant\Brand;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        $productsData = [
            [
                'name' => 'Sample T-Shirt Variant',
                'slug' => 'sample-t-shirt-variant',
                'sku' => 'SAMP-TSHIRT-VAR-001',
                'type' => 'variant',
                'is_active' => true,
                'options' => [
                    'Color' => ['Red', 'Blue'],
                    'Size' => ['S', 'M'],
                ],
                'price' => 19.99,
                'regular_price' => 24.99,
            ],
            [
                'name' => 'Sneakers Variant',
                'slug' => 'sneakers-variant',
                'sku' => 'SNEAK-VAR-001',
                'type' => 'variant',
                'is_active' => true,
                'options' => [
                    'Color' => ['Black', 'White'],
                    'Size' => ['40', '41', '42'],
                ],
                'price' => 49.99,
                'regular_price' => 59.99,
            ],
            [
                'name' => 'Hoodie Variant',
                'slug' => 'hoodie-variant',
                'sku' => 'HOODIE-VAR-001',
                'type' => 'variant',
                'is_active' => true,
                'options' => [
                    'Color' => ['Gray', 'Black'],
                    'Size' => ['M', 'L', 'XL'],
                ],
                'price' => 29.99,
                'regular_price' => 39.99,
            ],
        ];

        // Create a brand for the products
        $brand = Brand::create([
            'name' => 'SaleTodayStore',
            'slug' => 'saletodaystore',
            'logo_url' => null,
        ]);

        foreach ($productsData as $productData) {
            // Check if product already exists by slug
            $product = Product::where('slug', $productData['slug'])->first();
            if (!$product) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'sku' => $productData['sku'],
                    'type' => $productData['type'],
                    'is_active' => $productData['is_active'],
                    'brand_id' => $brand->id,
                    'status' => 'published',
                ]);
            }

            // Create variant options
            $optionModels = [];
            foreach ($productData['options'] as $optionName => $values) {
                $optionModels[$optionName] = VariantOption::create([
                    'product_id' => $product->id,
                    'name' => $optionName,
                ]);
            }

            // Generate all combinations of option values
            $combinations = $this->cartesian(array_values($productData['options']));

            foreach ($combinations as $combination) {
                $variantName = implode(' ', $combination);
                $skuSuffix = strtoupper(implode('', array_map(function($v){ return substr($v,0,1); }, $combination)));
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantName,
                    'sku' => $product->sku . '-' . $skuSuffix,
                    'price' => $productData['price'],
                    'regular_price' => $productData['regular_price'],
                    'stock_qty' => 10,
                    'is_default' => false,
                    'weight' => 0.2,
                    'height' => 2,
                    'width' => 20,
                    'depth' => 30,
                ]);

                // Attach option values to variant
                $i = 0;
                foreach (array_keys($productData['options']) as $optionName) {
                    VariantOptionValue::create([
                        'variant_option_id' => $optionModels[$optionName]->id,
                        'product_variant_id' => $variant->id,
                        'value' => $combination[$i],
                    ]);
                    $i++;
                }
            }
        }
    }

    // Helper function to generate cartesian product of arrays
    private function cartesian($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
