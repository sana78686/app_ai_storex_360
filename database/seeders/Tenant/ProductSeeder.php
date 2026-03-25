<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Product;
use App\Models\Tenant\ProductTranslation;
use App\Models\Tenant\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        $topLevelIds = $categories->whereNull('parent_id')->pluck('id')->toArray();
        $childIds = $categories->whereNotNull('parent_id')->pluck('id')->toArray();
        $allIds = array_merge($topLevelIds, $childIds);

        $products = [
            ['name' => 'Classic Cotton T-Shirt', 'price' => 19.99, 'compare_at_price' => 24.99, 'sku' => 'APT-001'],
            ['name' => 'Denim Jeans Slim Fit', 'price' => 49.99, 'compare_at_price' => 59.99, 'sku' => 'APT-002'],
            ['name' => 'Running Sneakers', 'price' => 79.99, 'compare_at_price' => 99.99, 'sku' => 'APT-003'],
            ['name' => 'Leather Handbag', 'price' => 89.99, 'compare_at_price' => 119.99, 'sku' => 'APT-004'],
            ['name' => 'Summer Dress', 'price' => 44.99, 'compare_at_price' => 54.99, 'sku' => 'APT-005'],
            ['name' => 'Wireless Bluetooth Headphones', 'price' => 59.99, 'compare_at_price' => 79.99, 'sku' => 'ELC-001'],
            ['name' => 'USB-C Laptop Charger', 'price' => 34.99, 'compare_at_price' => 44.99, 'sku' => 'ELC-002'],
            ['name' => 'Portable Power Bank 10000mAh', 'price' => 29.99, 'compare_at_price' => 39.99, 'sku' => 'ELC-003'],
            ['name' => 'Mechanical Keyboard', 'price' => 89.99, 'compare_at_price' => 109.99, 'sku' => 'ELC-004'],
            ['name' => 'Wireless Mouse', 'price' => 24.99, 'compare_at_price' => 32.99, 'sku' => 'ELC-005'],
            ['name' => 'Garden Hose 25m', 'price' => 34.99, 'compare_at_price' => 44.99, 'sku' => 'HNG-001'],
            ['name' => 'Indoor Plant Pot Set', 'price' => 22.99, 'compare_at_price' => 29.99, 'sku' => 'HNG-002'],
            ['name' => 'Kitchen Knife Set 5-Piece', 'price' => 69.99, 'compare_at_price' => 89.99, 'sku' => 'HNG-003'],
            ['name' => 'LED Desk Lamp', 'price' => 39.99, 'compare_at_price' => 49.99, 'sku' => 'HNG-004'],
            ['name' => 'Outdoor Garden Chair', 'price' => 54.99, 'compare_at_price' => 69.99, 'sku' => 'HNG-005'],
            ['name' => 'Board Game Family Edition', 'price' => 29.99, 'compare_at_price' => 36.99, 'sku' => 'TYG-001'],
            ['name' => 'Building Blocks Set 200pcs', 'price' => 44.99, 'compare_at_price' => 54.99, 'sku' => 'TYG-002'],
            ['name' => 'Jigsaw Puzzle 1000 Pieces', 'price' => 18.99, 'compare_at_price' => 24.99, 'sku' => 'TYG-003'],
            ['name' => 'Card Game Deck', 'price' => 12.99, 'compare_at_price' => 15.99, 'sku' => 'TYG-004'],
            ['name' => 'Stuffed Animal Plush', 'price' => 19.99, 'compare_at_price' => 24.99, 'sku' => 'TYG-005'],
            ['name' => 'Moisturizing Face Cream', 'price' => 24.99, 'compare_at_price' => 31.99, 'sku' => 'HLB-001'],
            ['name' => 'Shampoo & Conditioner Set', 'price' => 18.99, 'compare_at_price' => 23.99, 'sku' => 'HLB-002'],
            ['name' => 'Electric Toothbrush', 'price' => 39.99, 'compare_at_price' => 49.99, 'sku' => 'HLB-003'],
            ['name' => 'Lipstick Set 3 Colors', 'price' => 22.99, 'compare_at_price' => 28.99, 'sku' => 'HLB-004'],
            ['name' => 'Sunscreen SPF 50', 'price' => 14.99, 'compare_at_price' => 18.99, 'sku' => 'HLB-005'],
        ];

        $defaultLocale = config('app.fallback_locale', 'en');
        $description = 'Quality product. Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

        foreach ($products as $index => $data) {
            $name = $data['name'];
            $slugBase = Str::slug($name);
            $slug = $slugBase;
            $attempt = 0;
            while (ProductTranslation::where('locale', $defaultLocale)->where('slug', $slug)->exists()) {
                $attempt++;
                $slug = $slugBase . '-' . $attempt;
            }

            $product = Product::create([
                'name' => $name,
                'detailed_description' => $description,
                'status' => 'active',
                'price' => $data['price'],
                'compare_at_price' => $data['compare_at_price'] ?? null,
                'quantity' => rand(5, 50),
                'sku' => $data['sku'],
                'inventory_tracked' => true,
                'charge_tax' => true,
            ]);

            $product->translations()->create([
                'locale' => $defaultLocale,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ]);

            $categoryIndex = (int) floor($index / 5) % max(1, count($topLevelIds));
            $parentId = $topLevelIds[$categoryIndex];
            $childCats = $categories->where('parent_id', $parentId)->pluck('id')->toArray();

            if (!empty($childCats)) {
                $product->categories()->sync([$childCats[array_rand($childCats)]]);
            } else {
                $product->categories()->sync([$parentId]);
            }
        }
    }
}
