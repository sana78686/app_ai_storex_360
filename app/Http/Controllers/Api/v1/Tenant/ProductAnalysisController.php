<?php
namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAnalysisController extends Controller
{
    public function analyze(Request $request)
    {
        $title = $request->input('title');
        if (!$title) return response()->json(['error' => 'No title provided'], 422);

        // 1. Predict Category
        // For speed, we search keywords from the title in our category names
        $predictedCategory = $this->smartCategoryMatch($title);

        // 2. Generate SEO Meta Title (Shopify Best Practice)
        // Format: [Title] | [Store Name] - Limited to 60-70 characters
        $metaTitle = Str::limit($title, 60) . " | " . config('app.name');

        return response()->json([
            'category' => $predictedCategory ? [
                'id' => $predictedCategory->id,
                'name' => $predictedCategory->name,
                'breadcrumb' => $this->getBreadcrumb($predictedCategory),
            ] : null,
            'seo' => [
                'meta_title' => $metaTitle,
            ]
        ]);
    }

   private function smartCategoryMatch($title)
{
    $words = explode(' ', strtolower($title));
    // Filter out common "noise" words to focus on real products
    $stopWords = ['years', 'with', 'made', 'from', 'each', 'pack', 'size', 'storage', 'weight', 'color'];
    $filteredWords = array_filter($words, fn($w) => strlen($w) > 3 && !in_array($w, $stopWords));

    // Best Practice: Order by matches that appear in the title AND are deep in the tree
    return Category::where(function($query) use ($filteredWords) {
            foreach ($filteredWords as $word) {
                $query->orWhere('name', 'LIKE', "%$word%");
            }
        })
        /* LOGIC:
           1. We look for categories whose names are actually IN the title
           2. We prioritize categories that have a parent (more specific)
        */
        ->get()
        ->sortByDesc(function($category) use ($title) {
            // Give higher points if the exact category name exists in the product title
            return str_contains(strtolower($title), strtolower($category->name)) ? 100 : 0;
        })
        ->first();
}

    private function getBreadcrumb($category)
    {
        $path = [$category->name];
        while ($category->parent) {
            $category = $category->parent;
            array_unshift($path, $category->name);
        }
        return implode(' > ', $path);
    }
}
