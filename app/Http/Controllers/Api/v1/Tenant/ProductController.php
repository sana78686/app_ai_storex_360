<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use Illuminate\Validation\Rule;
use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\ProductsImport;
use App\Models\Tenant\Product;
use App\Models\Tenant\Tag;
use App\Models\Tenant\VariantOption;
use App\Models\Tenant\ProductOption;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ImportProductsJob;
use App\Services\MediaUploadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class ProductController extends Controller
{
    /**
     * Get paginated list of products
     */
public function index(Request $request)
{
    try {
        $locale = config('app.fallback_locale', 'en');
        $search = $request->get('search'); // Get the search query from Vue
        $categoryId = $request->get('category_id');

        $products = Product::query()
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($search, function ($query) use ($search, $locale) {
                $query->where(function ($q) use ($search, $locale) {
                    // Search by SKU
                    $q->where('sku', 'LIKE', "%{$search}%")
                    // OR Search by Name in translations
                    ->orWhereHas('translations', function ($translationQ) use ($search, $locale) {
                        $translationQ->where('locale', $locale)
                                     ->where('name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->with([
                'translations' => function ($q) use ($locale) {
                    $q->where('locale', $locale);
                },
                'categories',
                'media',
                'variants.media',
                'variants.optionValues.translations' => function ($q) use ($locale) {
                    $q->where('locale', $locale);
                }
            ])
            ->when($search, fn ($q) => $q->limit(80), fn ($q) => $q->limit($categoryId ? 100 : 25))
            ->get()
            // ... (rest of your mapping logic remains the same)
            ->map(function ($product) use ($locale) {

                // 🔥 Fallback handling
                $translation = $product->translations->first()
                    ?? $product->translations()->where('locale', config('app.fallback_locale'))->first();

                $product->name = $translation?->name;
                $product->description = $translation?->description;

                // Clean response (optional)
                unset($product->translations);

                // 🔥 Variant option values translation flatten
                foreach ($product->variants as $variant) {
                    foreach ($variant->optionValues as $value) {
                        $valueTranslation = $value->translations->first()
                            ?? $value->translations()
                                ->where('locale', config('app.fallback_locale'))
                                ->first();

                        $value->name = $valueTranslation?->name;
                        unset($value->translations);
                    }
                }

                return $product;
            });

        return response()->json([
            'success' => true,
            'message' => 'Products fetched successfully',
            'products' => $products
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch products',
            'error' => $e->getMessage()
        ], 500);
    }
}



    /**
     * Show single product by ID or slug
     */
    public function show($id)
{
    try {

        $defaultLocale = config('app.fallback_locale', 'en');

        $product = Product::with([

            // Product translations
            'translations',

            // Product media
            'media',

            // Categories
            'categories',

            // Variants
            'variants.media',

            // Variant option values with translations
            'variants.optionValues.translations',

            // Variant option (parent option) translations
            'variants.optionValues.option.translations',

            // All product variant options
            'variantOptions.translations',
            'variantOptions.values.translations',

        ])->findOrFail($id);

        $product->tenant_id = tenant('id');

        return response()->json([
            'status'  => 'success',
            'message' => 'Product fetched successfully',
            'data'    => $product,
        ], 200);

    } catch (ModelNotFoundException $e) {

        return response()->json([
            'status'  => 'fail',
            'message' => 'Product not found',
            'errors'  => [],
        ], 404);

    } catch (\Throwable $e) {

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to fetch product',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



public function updateStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => [
            'required',
            Rule::in(['active', 'draft', 'archived', 'published'])
        ],
    ]);

    try {
        $product = Product::findOrFail($id);

        $product->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'data' => [
                'id' => $product->id,
                'status' => $product->status
            ]
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to update product status',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // public function show($idOrSlug)
    // {
    //     try {
    //         $product = Product::where('slug', $idOrSlug)
    //             ->orWhere('id', $idOrSlug)
    //             ->with(['media', 'variants.values.option', 'categories', 'infoFields',  'technicalInfos', 'variantOptions.values'])
    //             ->firstOrFail();
    //         $product->tenant_id = tenant('id');

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Product fetched successfully',
    //             'data' => $product,

    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'status' => 'fail',
    //             'message' => 'Product not found',
    //             'errors' => [],
    //         ], 404);
    //     } catch (Exception $e) {
    //         return $this->errorResponse('Failed to fetch product', $e);
    //     }
    // }

    /**
     * Store new product (with variants, categories, tags, images)
     */
     public function store(Request $request, MediaUploadService $mediaService)
    {
        // DB::beginTransaction();

        try {

            /* =======================
             | 1. Create Product
             ======================= */
                    $data = $request->only([
             'sku',
             'price','compare_at_price','track_quantity','stock',
             'type','is_active','is_featured','allow_backorder',
             'brand_id','status'
         ]);

            $data['name'] = $request->input('name') ?: 'Untitled';
            $data['detailed_description'] = $request->input('detailed_description');

            $product = Product::create($data);
            // Generate slug manually
            // $data['slug'] = Str::slug($request->name);
             $defaultLocale = config('app.fallback_locale', 'en');

            $translations = $request->input('translations', [
                $defaultLocale => [
                    'name' => $request->input('name'),
                    'description' => $request->input('detailed_description'),
                ]
            ]);

            foreach ($translations as $locale => $data) {
                $product->translations()->create([
                    'locale' => $locale,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => $data['description'] ?? null,
                ]);
            }


            /* =======================
             | 2. Categories & Tags
             ======================= */
              if ($request->filled('categories_id')) {
            // Handle comma-separated string or array
            $categoryIds = is_array($request->categories_id)
                ? $request->categories_id
                : explode(',', $request->categories_id);

            $product->categories()->sync($categoryIds);
          }

            // if ($request->tags) {
            //     $product->tags()->sync($request->tags);
            // }

            /* =======================
             | 3. Product Media
             ======================= */
           if ($request->hasFile('images')) {
            // ✅ uploaded files
            foreach ($request->file('images') as $i => $file) {
                $upload = $mediaService->upload($file, 'product', $product->id);

                $product->media()->create([
                    'type'      => 'product',
                    'entity_id' => $product->id,
                    'file_key'  => $upload['file_key'],
                    'cdn_url'   => $upload['cdn_url'],
                    'mime_type' => $upload['mime'],
                    'size'      => $upload['size'],
                    'is_main'   => $i === 0,
                ]);
            }
            } elseif ($request->filled('images')) {
            // ✅ preview URLs sent from frontend
            foreach ($request->images as $i => $file) {
                $product->media()->create([
                    'type'      => 'product',
                    'entity_id' => $product->id,
                    'file_key'  => $file['file_key'] ?? null,
                    'cdn_url'   => $file['cdn_url'] ?? $file['preview'] ?? null,
                    'is_main'   => $i === 0,
                ]);
            }
            }

                       /* =======================
                        | 4. Options + Values
                        ======================= */
                      /* =======================
            | 4. Variant Options
            ======================= */
          if ($request->filled('variants') && is_array($request->variants)) {

              $defaultLocale = config('app.fallback_locale', 'en');

              foreach ($request->variants as $variantData) {

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Create Variant
        |--------------------------------------------------------------------------
        */

        $variant = $product->variants()->create([
            'title'    => $variantData['title'] ?? 'Default Title',
            'price'    => $variantData['price'] ?? 0,
            'quantity' => $variantData['qty'] ?? 0,
            'sku'      => $variantData['sku'] ?? 'SKU-' . rand(1000, 9999),
        ]);

        $valueIds = [];

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Handle Options + Values (TRANSLATION SAFE)
        |--------------------------------------------------------------------------
        */

       if (!empty($variantData['options']) && is_array($variantData['options'])) {

     foreach ($variantData['options'] as $optionName => $valueName) {

        // 1️⃣ Find or Create Option (by default locale)
        $option = $product->variantOptions()
            ->whereHas('translations', function ($q) use ($defaultLocale, $optionName) {
                $q->where('locale', $defaultLocale)
                  ->where('name', $optionName);
            })
            ->first();

        if (!$option) {
            $option = $product->variantOptions()->create([
                'position' => 0,
            ]);

            $option->translations()->create([
                'locale' => $defaultLocale,
                'name'   => $optionName,
            ]);
        }

        // 2️⃣ Find or Create Value
        $value = $option->values()
            ->whereHas('translations', function ($q) use ($defaultLocale, $valueName) {
                $q->where('locale', $defaultLocale)
                  ->where('name', $valueName);
            })
            ->first();

        if (!$value) {
            $value = $option->values()->create([
                'code' => strtoupper(str_replace(' ', '_', $valueName)),
            ]);

            $value->translations()->create([
                'locale' => $defaultLocale,
                'name'   => $valueName,
            ]);
        }

        $valueIds[] = $value->id;
            }
            }


        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Attach Option Values to Variant
        |--------------------------------------------------------------------------
        */

        if (!empty($valueIds)) {
            $variant->optionValues()->sync($valueIds);
        }

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Media Upload
        |--------------------------------------------------------------------------
        */

        if (!empty($variantData['media']) && is_array($variantData['media'])) {

            foreach ($variantData['media'] as $file) {

                $upload = $mediaService->upload(
                    $file,
                    'product',
                    $product->id
                );

                $variant->media()->create([
                    'file_key'  => $upload['file_key'],
                    'cdn_url'   => $upload['cdn_url'],
                    'mime_type' => $upload['mime'],
                    'size'      => $upload['size'],
                ]);
            }
        }
              }
          }








            // DB::commit();
          $product->load('media', 'categories','translations',  'variants', 'variantOptions');
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product->load('media','variants.media','variantOptions.values')
            ], 201);

        } catch (\Throwable $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Product creation failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
//

    /**
     * Update product basic details only
     */
   public function update($id, Request $request, MediaUploadService $mediaService)
{
    DB::beginTransaction();

    try {
        $product = Product::with([
            'media',
            'variants.media',
            'variants.optionValues',
            'variantOptions.values',
            'categories'
        ])->findOrFail($id);

        /* =======================
         | 1. Update Product
         ======================= */
       $defaultLocale = config('app.fallback_locale', 'en');

          $product->update($request->only([
              'sku',
              'price','compare_at_price','track_quantity','stock',
              'type','is_active','is_featured','allow_backorder',
              'brand_id','status'
          ]));

          // Update or Create Translation
          $product->translations()->updateOrCreate(
              ['locale' => $defaultLocale],
              [
                  'name' => $request->input('name'),
                  'slug' => Str::slug($request->input('name')),
                  'description' => $request->input('detailed_description'),
              ]
          );


        /* =======================
         | 2. Categories
         ======================= */
        if ($request->filled('categories_id')) {
            $categoryIds = is_array($request->categories_id)
                ? $request->categories_id
                : explode(',', $request->categories_id);

            $product->categories()->sync($categoryIds);
        }

        /* =======================
         | 3. Product Media
         ======================= */

        // IDs sent from frontend that should remain
        $keepMediaIds = collect($request->existing_media ?? [])
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete removed media
        $product->media()
            ->whereNotIn('id', $keepMediaIds)
            ->delete();

        // Upload new media
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $upload = $mediaService->upload($file, 'product', $product->id);

                $product->media()->create([
                    'type'      => 'product',
                    'entity_id' => $product->id,
                    'file_key'  => $upload['file_key'],
                    'cdn_url'   => $upload['cdn_url'],
                    'mime_type' => $upload['mime'],
                    'size'      => $upload['size'],
                    'is_main'   => $i === 0,
                ]);
            }
        }

        /* =======================
         | 4. Variants
         ======================= */
        $incomingVariantIds = collect($request->variants)
            ->pluck('id')
            ->filter()
            ->toArray();

        // Delete removed variants
        $product->variants()
            ->whereNotIn('id', $incomingVariantIds)
            ->delete();

        foreach ($request->variants ?? [] as $variantData) {

            // Update or create variant
            $variant = $product->variants()->updateOrCreate(
                ['id' => $variantData['id'] ?? null],
                [
                    'title'    => $variantData['title'],
                    'price'    => $variantData['price'],
                    'quantity' => $variantData['qty'],
                    'sku'      => $variantData['sku'],
                ]
            );

            /* =======================
             | Variant Options
             ======================= */
            $valueIds = [];

           $defaultLocale = config('app.fallback_locale', 'en');
$valueIds = [];

foreach ($variantData['options'] ?? [] as $optionName => $valueName) {

    // 1️⃣ Find Option by Translation
    $option = $product->variantOptions()
        ->whereHas('translations', function ($q) use ($defaultLocale, $optionName) {
            $q->where('locale', $defaultLocale)
              ->where('name', $optionName);
        })
        ->first();

    if (!$option) {
        $option = $product->variantOptions()->create([
            'position' => 0,
        ]);

        $option->translations()->create([
            'locale' => $defaultLocale,
            'name'   => $optionName,
        ]);
    }

    // 2️⃣ Find Value by Translation
    $value = $option->values()
        ->whereHas('translations', function ($q) use ($defaultLocale, $valueName) {
            $q->where('locale', $defaultLocale)
              ->where('name', $valueName);
        })
        ->first();

    if (!$value) {
        $value = $option->values()->create([
            'code' => strtoupper(str_replace(' ', '_', $valueName)),
        ]);

        $value->translations()->create([
            'locale' => $defaultLocale,
            'name'   => $valueName,
        ]);
    }

    $valueIds[] = $value->id;
}

$variant->optionValues()->sync($valueIds);


            /* =======================
             | Variant Media
             ======================= */

            $keepVariantMediaIds = collect($variantData['existing_media'] ?? [])
                ->pluck('id')
                ->filter()
                ->toArray();

            $variant->media()
                ->whereNotIn('id', $keepVariantMediaIds)
                ->delete();

            if (!empty($variantData['media'])) {
                foreach ($variantData['media'] as $file) {
                    $upload = $mediaService->upload($file, 'product', $product->id);

                    $variant->media()->create([
                        'file_key'  => $upload['file_key'],
                        'cdn_url'   => $upload['cdn_url'],
                        'mime_type' => $upload['mime'],
                        'size'      => $upload['size'],
                    ]);
                }
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh([
                'media',
                'categories',
                'variants.media',
                'variants.optionValues',
                'variantOptions.values'
            ])
        ], 200);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Product update failed',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * Delete a product
     */
    public function destroy($id)
    {
        try {
            $p = Product::findOrFail($id);
            $p->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Product not found',
                'errors' => [],
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete product', $e);
        }
    }

    /**
     * Generate a unique product slug
     */
    protected function generateUniqueSlug(string $name, ?string $sku = null, $variants = null): string
    {
        // ✅ Decode if variants is a JSON string
        if (is_string($variants)) {
            $variants = json_decode($variants, true);
        }

        $base = Str::slug($name);
        $extra = null;

        if ($sku) {
            $extra = Str::slug($sku);
        } elseif (is_array($variants) && count($variants) > 0) {
            $first = $variants[0];

            // Handle two possible shapes:
            // 1. [{"option_name": "color", "option_value": "red"}]
            // 2. [{"values": [{"option": "color", "value": "red"}]}]
            if (! empty($first['values'])) {
                $vals = array_map(fn ($v) => Str::slug($v['value']), $first['values']);
                $extra = implode('-', $vals);
            } elseif (! empty($first['option_value'])) {
                $extra = Str::slug($first['option_value']);
            }
        }

        $candidate = $extra ? "{$base}-{$extra}" : $base;
        $original = $candidate;
        $i = 1;

        while (Product::where('slug', $candidate)->exists()) {
            $candidate = "{$original}-{$i}";
            $i++;
        }

        return $candidate;
    }

    /**
     * Centralized JSON error response
     */
    private function errorResponse($message, Exception $e)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => [$e->getMessage()],
        ], 500);
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    /**
     * Import products from Excel using queue job
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Excel files only, max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Store the uploaded file temporarily
            $file = $request->file('file');
            $tenantId = tenant('id');

            if (!$tenantId) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Tenant context not found',
                ], 400);
            }

            // Store file in storage/app/imports directory
            $filePath = $file->store('imports', 'local');

            // Verify file was stored
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to store uploaded file',
                ], 500);
            }

            // Generate unique job ID
            $jobId = uniqid('import_', true);

            try {
                // Check queue connection
                $queueConnection = config('queue.default');

                // Dispatch job asynchronously with job ID
                if ($queueConnection === 'sync') {
                    // If sync, run immediately but still track status
                    $this->setJobStatus($jobId, 'processing');
                    ImportProductsJob::dispatchSync($filePath, $tenantId, $jobId);
                    $this->setJobStatus($jobId, 'completed');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Products imported successfully.',
                        'job_id' => $jobId,
                    ], 200);
                } else {
                    // Async dispatch - NOTE: Queue worker must be running!
                    // Run: php artisan queue:work (or queue:listen)
                    $job = ImportProductsJob::dispatch($filePath, $tenantId, $jobId);

                    // Store job status in cache (expires in 10 minutes)
                    $this->setJobStatus($jobId, 'queued');

                    Log::info("Import job dispatched", [
                        'job_id' => $jobId,
                        'file_path' => $filePath,
                        'tenant_id' => $tenantId,
                        'queue_connection' => $queueConnection
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Import job queued successfully. Processing in the background. Make sure queue worker is running!',
                        'job_id' => $jobId,
                    ], 200);
                }
            } catch (\Exception $dispatchException) {
                // Clean up file if dispatch fails
                if (Storage::disk('local')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                }

                // Update cache to failed
                $this->setJobStatus($jobId, 'failed');

                Log::error('Job dispatch/execution failed: ' . $dispatchException->getMessage(), [
                    'trace' => $dispatchException->getTraceAsString(),
                    'queue_connection' => config('queue.default')
                ]);

                throw $dispatchException; // Re-throw to be caught by outer catch
            }

        } catch (\Exception $e) {
            Log::error('Failed to queue product import: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Clean up file on error
            if (isset($filePath) && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to queue import job: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred while queuing the import job.',
            ], 500);
        }
    }

    /**
     * Set job status in cache (bypassing tenancy tags)
     * Uses database cache table directly to avoid tagging issues
     */
    private function setJobStatus(string $jobId, string $status): void
    {
        try {
            // Use DB directly to store job status, bypassing Cache facade that applies tags
            $cacheKey = config('cache.prefix') . "import_job_{$jobId}";
            $expiration = now()->addMinutes(10)->timestamp;

            // Use central database connection for cache
            DB::connection('mysql')->table('cache')->updateOrInsert(
                ['key' => $cacheKey],
                [
                    'value' => serialize($status),
                    'expiration' => $expiration
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Failed to store job status: ' . $e->getMessage());
        }
    }

    /**
     * Get job status from cache (bypassing tenancy tags)
     */
    private function getJobStatus(string $jobId): ?string
    {
        try {
            $cacheKey = config('cache.prefix') . "import_job_{$jobId}";

            $cached = DB::connection('mysql')->table('cache')
                ->where('key', $cacheKey)
                ->where('expiration', '>', now()->timestamp)
                ->first();

            return $cached ? unserialize($cached->value) : null;
        } catch (\Exception $e) {
            Log::warning('Failed to get job status: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check import job status
     */
    public function checkImportStatus(Request $request)
    {
        $jobId = $request->input('job_id');

        if (!$jobId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Job ID is required',
            ], 400);
        }

        try {
            // Check cache first using helper method
            $cachedStatus = $this->getJobStatus($jobId);

            if ($cachedStatus === 'completed') {
                return response()->json([
                    'status' => 'completed',
                    'message' => 'Import completed successfully',
                ], 200);
            }

            if ($cachedStatus === 'failed') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Import job failed',
                ], 200);
            }

            if ($cachedStatus === 'queued' || $cachedStatus === 'processing') {
                return response()->json([
                    'status' => 'processing',
                    'message' => 'Import is still processing',
                ], 200);
            }

            // If no cache status found, check if it might be in failed_jobs
            // Note: Our custom job IDs won't match Laravel's job IDs, so we can't query jobs table directly
            // If cache expired or was cleared, assume still processing
            return response()->json([
                'status' => 'processing',
                'message' => 'Import is still processing',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to check import job status: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check job status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
