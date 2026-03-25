<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Category;
use App\Models\Tenant\CategoryTranslation;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $taxonomy = [
            'Apparel & Accessories' => [
                'Clothing' => ['Shirts', 'Pants', 'Dresses', 'Activewear', 'Outerwear'],
                'Shoes' => ['Athletic Shoes', 'Boots', 'Sandals', 'Formal Shoes', 'Sneakers'],
                'Accessories' => ['Handbags', 'Belts', 'Sunglasses', 'Hats', 'Wallets'],
            ],
            'Electronics' => [
                'Computers' => ['Laptops', 'Desktops', 'Monitors', 'Tablets', 'Components'],
                'Mobile Phones' => ['Smartphones', 'Cases', 'Chargers', 'Power Banks', 'Cables'],
                'Audio' => ['Headphones', 'Speakers', 'Microphones', 'Amplifiers', 'Home Theater'],
            ],
            'Home & Garden' => [
                'Furniture' => ['Beds', 'Chairs', 'Tables', 'Sofas', 'Desks'],
                'Kitchen' => ['Cookware', 'Small Appliances', 'Utensils', 'Bakeware', 'Cutlery'],
                'Garden' => ['Tools', 'Outdoor Decor', 'Plants', 'Mowers', 'Grills'],
            ],
            'Toys & Games' => [
                'Toys' => ['Action Figures', 'Dolls', 'Building Sets', 'Stuffed Animals', 'Ride-On Toys'],
                'Games' => ['Board Games', 'Card Games', 'Puzzles', 'Video Games', 'Outdoor Play'],
            ],
            'Health & Beauty' => [
                'Personal Care' => ['Skincare', 'Haircare', 'Bath & Body', 'Oral Care', 'Shaving'],
                'Cosmetics' => ['Face Makeup', 'Eye Makeup', 'Lipstick', 'Nail Care', 'Brushes'],
            ]
        ];

        foreach ($taxonomy as $parent => $children) {
            $this->createCategoryTree($parent, $children);
        }
    }

    /**
     * Recursively create multilingual categories
     */
    private function createCategoryTree(string $name, array|string $children, $parentId = null): void
    {
        // Create category (categories table: parent_id, name, is_active, timestamps)
        $category = Category::create([
            'parent_id' => $parentId,
            'name' => $name,
            'is_active' => true,
        ]);

        // category_translations: locale, name, slug (unique per locale), description
        $locale = config('app.fallback_locale', 'en');
        $slugBase = Str::slug($name);
        $slug = $slugBase;
        $attempt = 0;
        while (CategoryTranslation::where('locale', $locale)->where('slug', $slug)->exists()) {
            $attempt++;
            $slug = $slugBase . '-' . $attempt;
        }
        $category->translations()->create([
            'locale' => $locale,
            'name' => $name,
            'slug' => $slug,
            'description' => null,
        ]);

        // Recursively create children
        if (is_array($children)) {
            foreach ($children as $childName => $subChildren) {
                $nextName = is_numeric($childName) ? $subChildren : $childName;
                $this->createCategoryTree($nextName, $subChildren, $category->id);
            }
        }
    }
}
