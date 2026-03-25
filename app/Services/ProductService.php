<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantOption;
use App\Models\VariantValue;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // 1. Generate slug (do not overwrite if provided)
            $slug = $data['slug'] ?? $this->generateUniqueSlug($data['name'], $data['sku'] ?? null, $data['variants'] ?? null);

            // 2. Create product basic
            $product = Product::create(array_merge($data, ['slug' => $slug]));

            // 3. Categories
            if (!empty($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            // 4. Tags (as names => create if not exists)
            if (!empty($data['tags'])) {
                $tagIds = [];
                foreach ($data['tags'] as $tagName) {
                    $tag = Tag::firstOrCreate(['slug' => Str::slug($tagName)], ['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                $product->tags()->sync($tagIds);
            }

            // 5. Images
            if (!empty($data['images'])) {
                foreach ($data['images'] as $i => $img) {
                    $product->images()->create([
                        'url' => $img['url'],
                        'is_main' => $img['is_main'] ?? ($i===0)
                    ]);
                }
            }

            // 6. Variant options + values
            $optionMap = []; // option name => option id
            if (!empty($data['variant_options'])) {
                foreach ($data['variant_options'] as $option) {
                    $optModel = VariantOption::firstOrCreate(['name' => $option['name']]);
                    foreach ($option['values'] as $val) {
                        VariantValue::firstOrCreate([
                            'variant_option_id' => $optModel->id,
                            'value' => $val
                        ]);
                    }
                    $optionMap[$option['name']] = $optModel->id;
                }
            }

            // 7. Variants (product_variants + pivot to variant_values)
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $idx => $v) {
                    $variant = $product->variants()->create([
                        'name' => $v['name'] ?? ($product->name . ' - Variant'),
                        'sku' => $v['sku'] ?? null,
                        'price' => $v['price'] ?? $product->price,
                        'stock' => $v['stock'] ?? 0,
                        'is_default' => $v['is_default'] ?? ($idx === 0)
                    ]);

                    // attach values: expect values as [{option: "Size", value: "S"}]
                    if (!empty($v['values'])) {
                        $valueIds = [];
                        foreach ($v['values'] as $vv) {
                            // find variant option id
                            $opt = VariantOption::firstOrCreate(['name' => $vv['option']]);
                            $valModel = VariantValue::firstOrCreate([
                                'variant_option_id' => $opt->id,
                                'value' => $vv['value']
                            ]);
                            $valueIds[] = $valModel->id;
                        }
                        $variant->values()->sync($valueIds);
                    }
                }
            } else {
                // if no variants and product is simple and price provided set stock and price
                if (($product->type === 'simple' || empty($product->type)) && isset($data['stock'])) {
                    $product->stock = $data['stock'];
                    $product->save();
                }
            }

            return $product->load(['images','variants.values.option','categories','tags']);
        });
    }

    protected function generateUniqueSlug(string $name, ?string $sku = null, ?array $variants = null): string
    {
        $base = Str::slug($name);
        $extra = null;

        if ($sku) {
            $extra = Str::slug($sku);
        } elseif ($variants && is_array($variants) && count($variants) > 0) {
            // create an indicative suffix from first variant's values
            $first = $variants[0];
            if (!empty($first['values']) && is_array($first['values'])) {
                $vals = array_map(function($v){ return Str::slug($v['value']); }, $first['values']);
                $extra = implode('-', $vals);
            }
        }

        $candidate = $extra ? "{$base}-{$extra}" : $base;
        $original = $candidate;
        $i = 1;
        while (\App\Models\Product::where('slug', $candidate)->exists()) {
            $candidate = "{$original}-{$i}";
            $i++;
        }
        return $candidate;
    }
}
