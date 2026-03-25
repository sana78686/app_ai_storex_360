<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List categories (tree)
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children.translations', 'translations'])
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show single category by slug (for frontend category pages)
    |--------------------------------------------------------------------------
    */

    public function showBySlug($slug)
    {
        $locale = config('app.fallback_locale', 'en');
        $category = Category::with([
            'parent.translations',
            'children.translations',
            'translations'
        ])
            ->whereHas('translations', function ($q) use ($slug, $locale) {
                $q->where('slug', $slug)->where('locale', $locale);
            })
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json([
            'category' => $category
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show single category by id
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $category = Category::with([
            'parent.translations',
            'children.translations',
            'translations'
        ])->findOrFail($id);

        return response()->json([
            'category' => $category
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create category (multilingual)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'locale' => 'nullable|string|max:5',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        $locale = $validated['locale'] ?? app()->getLocale();

        // 1️⃣ Create structure only
        $category = Category::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => true,
        ]);

        // 2️⃣ Create translation
        $category->translations()->create([
            'locale' => $locale,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'category' => $category->load('translations')
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update category or translation
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'locale' => 'nullable|string|max:5',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $locale = $validated['locale'] ?? app()->getLocale();

        // update structure
        $category->update([
            'parent_id' => $validated['parent_id'] ?? $category->parent_id,
            'is_active' => $validated['is_active'] ?? $category->is_active,
        ]);

        // update/create translation
        if (isset($validated['name']) || isset($validated['description'])) {

            $translation = $category->translations()->firstOrNew([
                'locale' => $locale
            ]);

            if (isset($validated['name'])) {
                $translation->name = $validated['name'];
                $translation->slug = Str::slug($validated['name']);
            }

            if (isset($validated['description'])) {
                $translation->description = $validated['description'];
            }

            $translation->save();
        }

        return response()->json([
            'category' => $category->load('translations')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}
