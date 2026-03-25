<?php

namespace App\Imports;

use App\Models\Tenant\Attribute;
use App\Models\Tenant\AttributeValue;
use App\Models\Tenant\Product;
use App\Models\Tenant\ProductAttribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithLimit, WithCustomValueBinder
{
    use SkipsFailures;

    /**
     * Limit the number of rows to import (set high to read all rows)
     */
    public function limit(): int
    {
        return 10000; // Read up to 10,000 rows
    }

    /**
     * Bind value to cell to ensure all cells are read
     */
    public function bindValue(Cell $cell, $value)
    {
        // Always bind the value, even if empty
        $cell->setValueExplicit($value, DataType::TYPE_STRING);
        return true;
    }

    /**
     * Normalize header name (convert to snake_case, lowercase)
     */
    private function normalizeHeaderName($header)
    {
        return strtolower(str_replace([' ', '-'], '_', trim($header)));
    }

    /**
     * Process collection - can be called directly or via Maatwebsite Excel
     */
    public function processCollection(Collection $rows)
    {
        // This is the same as collection() but can be called directly
        $this->collection($rows);
    }

    public function collection(Collection $rows)
    {
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        Log::debug("Starting product import. Total rows from Excel (excluding header): " . $rows->count());

        // Debug: Check what Excel is actually reading
        Log::debug("Collection type: " . get_class($rows));
        Log::debug("Is Collection empty? " . ($rows->isEmpty() ? 'YES' : 'NO'));
        Log::debug("First row exists? " . ($rows->first() ? 'YES' : 'NO'));

        if ($rows->first()) {
            $firstRow = $rows->first()->toArray();
            Log::debug("First row keys count: " . count($firstRow));
            Log::debug("First row keys: " . implode(', ', array_keys($firstRow)));
        }

        // Debug: Dump all Excel data to see what we're getting
        $allData = [];
        $rowCount = 0;
        foreach ($rows as $index => $row) {
            $rowCount++;
            $rowArray = $row->toArray();
            $allData[] = [
                'excel_row' => $index + 2, // Excel row number (row 1 is header)
                'collection_index' => $index,
                'data' => $rowArray,
                'product_id_raw' => $rowArray['product_id'] ?? 'NOT_FOUND',
                'product_title_raw' => $rowArray['product_title'] ?? 'NOT_FOUND',
            ];
        }

        Log::debug("Actually processed {$rowCount} rows in foreach loop");

        // Log sample of first few rows for debugging
        if (count($allData) > 0) {
            Log::debug("Sample first row data: " . json_encode($allData[0] ?? []));
        }

        // Log headers for reference
        if ($rows->first()) {
            $firstRowArray = $rows->first()->toArray();
            $csvHeaders = array_keys($firstRowArray);
            Log::debug("Excel headers found: " . implode(', ', $csvHeaders));
        }

        // Filter out completely empty rows (rows with no data at all)
        $filteredRows = $rows->filter(function ($row) {
            $rowArray = $row->toArray();
            // Check if row has any non-null, non-empty values
            foreach ($rowArray as $value) {
                if ($value !== null && trim((string)$value) !== '') {
                    return true; // Row has at least one non-empty value
                }
            }
            return false; // Row is completely empty
        });

        Log::debug("After filtering empty rows: " . $filteredRows->count() . " rows with data");

        // First pass: Identify duplicates by SKU (product_id)
        $skuMap = [];
        foreach ($filteredRows as $index => $row) {
            $productId = $this->getProductId($row);
            if (!empty($productId)) {
                if (!isset($skuMap[$productId])) {
                    $skuMap[$productId] = [];
                }
                // Store Excel row number (index + 2: +1 for 0-based, +1 for header row)
                $skuMap[$productId][] = $index + 2;
            }
        }

        // Log duplicates
        $duplicates = array_filter($skuMap, fn($indices) => count($indices) > 1);
        if (!empty($duplicates)) {
            Log::debug("Found " . count($duplicates) . " duplicate SKUs. Will update existing products.");
            foreach ($duplicates as $sku => $indices) {
                Log::debug("SKU '{$sku}' appears in Excel rows: " . implode(', ', $indices));
            }
        }

        DB::transaction(function () use ($filteredRows, &$importedCount, &$updatedCount, &$skippedCount) {
            $skipReasons = [
                'no_sku' => 0,
                'formula' => 0,
                'error' => 0,
            ];

            // Detect "Variant X" columns: ignore word "variant", use next word(s) as attribute name
            $firstRowKeys = $filteredRows->first() ? array_keys($filteredRows->first()->toArray()) : [];
            $variantColumns = $this->getVariantColumnsMap($firstRowKeys);

            foreach ($filteredRows as $index => $row) {
                // Convert row to array for inspection
                $rowArray = $row->toArray();

                // Excel row number = collection index + 2 (index 0 = Excel row 2, since row 1 is header)
                $excelRowNumber = $index + 2;

                // Debug: Log first 15 rows to see all data
                if ($index < 15) {
                    $productIdRaw = $rowArray['product_id'] ?? null;
                    Log::debug("Excel Row {$excelRowNumber} (collection index {$index}) - product_id: " . (is_null($productIdRaw) ? 'NULL' : "'{$productIdRaw}'"));
                }

                // Map Excel columns to database fields
                $productId = $this->getProductId($row);
                $productTitle = $this->getProductTitle($row);
                $description = $this->getDescription($row);
                $price = $this->getPrice($row);

                // Skip if no product_id (SKU) found - this is the only required field
                if (empty($productId) || trim((string)$productId) === '' || $productId === null) {
                    $skippedCount++;
                    $skipReasons['no_sku']++;

                    // Log first 15 skipped rows and every 10th skipped row after that
                    if ($index < 15 || $skippedCount <= 15 || $skippedCount % 10 == 0) {
                        $rawValue = $rowArray['product_id'] ?? null;
                        Log::warning("Skipping Excel Row {$excelRowNumber}: No product_id (SKU) found. Raw value: " . (is_null($rawValue) ? 'NULL' : "'{$rawValue}'"));
                    }
                    continue;
                }

                // If product_title is empty or a formula, use product_id as fallback name
                if (empty($productTitle) || trim($productTitle) === '' || $productTitle === null) {
                    $productTitle = 'Product ' . $productId; // Use SKU as name if title is missing
                } elseif (is_string($productTitle) && str_starts_with(trim($productTitle), '=')) {
                    // Skip formula in title, use product_id instead
                    $productTitle = 'Product ' . $productId;
                }


                // Check if product with this SKU already exists
                $existingProduct = Product::where('sku', $productId)->first();

                if(!empty($productTitle)){
                    $status = 'active';
                } else {
                    $status = 'draft';
                }
                // If product exists by SKU, update it; otherwise create new
                try {
                    $product = null;
                    if ($existingProduct) {
                        // Update existing product
                        $existingProduct->update([
                            'name'        => $productTitle,
                            'detailed_description' => $description ?? $existingProduct->detailed_description,
                            'price'       => $price ?? $existingProduct->price,
                            'status'      => $status,
                        ]);
                        $product = $existingProduct;
                        $updatedCount++;
                    } else {
                        // Create new product (explicitly exclude slug)
                        $productData = [
                            'name'        => $productTitle,
                            'sku'         => $productId,
                            'detailed_description' => $description,
                            'price'       => $price,
                            'status'      => $status,
                        ];
                        unset($productData['slug']);
                        $product = Product::create($productData);
                        $importedCount++;
                    }

                    // Sync variant columns: attribute name from header (after "Variant"), value from cell → attributes, attribute_values, product_attribute
                    if ($product && !empty($variantColumns)) {
                        $this->syncProductVariantAttributes($product, $rowArray, $variantColumns);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to save product at Excel Row {$excelRowNumber}: " . $e->getMessage(), [
                        'product_title' => $productTitle,
                        'product_id' => $productId,
                        'excel_row' => $excelRowNumber,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $skippedCount++;
                    $skipReasons['error']++;
                }
            }

            // Log skip reasons summary
            Log::debug("Skip reasons summary: " . json_encode($skipReasons));
        });

        Log::debug("Import completed. Imported: {$importedCount}, Updated: {$updatedCount}, Skipped: {$skippedCount}");
    }

    /**
     * Get map of Excel column keys that start with "Variant" → attribute name (the next word(s) after "Variant").
     * E.g. "Variant Größe" / "variant_größe" → "Größe", "Variant Besonderes Merkmal" → "Besonderes Merkmal".
     */
    private function getVariantColumnsMap(array $rowKeys): array
    {
        $map = [];
        foreach ($rowKeys as $key) {
            $keyStr = (string) $key;
            $lower = mb_strtolower($keyStr);
            // Match "variant_" or "variant " (ignore the word "variant")
            if (str_starts_with($lower, 'variant_')) {
                $rest = trim(mb_substr($keyStr, 8)); // length of "variant_"
            } elseif (str_starts_with($lower, 'variant ')) {
                $rest = trim(mb_substr($keyStr, 8)); // length of "variant "
            } else {
                continue;
            }
            if ($rest === '') {
                continue;
            }
            // Use rest as attribute name: replace underscores with spaces, ucwords for display
            $attributeName = ucwords(str_replace('_', ' ', $rest));
            $map[$keyStr] = $attributeName;
        }
        return $map;
    }

    /**
     * For a saved product: ensure attributes (from variant column names) and attribute_values (from cell values)
     * exist, then link product to them via product_attribute.
     */
    private function syncProductVariantAttributes(Product $product, array $rowArray, array $variantColumns): void
    {
        foreach ($variantColumns as $columnKey => $attributeName) {
            $rawValue = $rowArray[$columnKey] ?? null;
            if ($rawValue === null || trim((string) $rawValue) === '') {
                continue;
            }
            $valueStr = trim((string) $rawValue);

            $attribute = Attribute::firstOrCreate(
                ['name' => $attributeName],
                ['name' => $attributeName]
            );

            $attributeValue = AttributeValue::firstOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'value'        => $valueStr,
                ],
                [
                    'attribute_id' => $attribute->id,
                    'value'        => $valueStr,
                ]
            );

            ProductAttribute::firstOrCreate(
                [
                    'product_id'         => $product->id,
                    'attribute_id'       => $attribute->id,
                    'attribute_value_id' => $attributeValue->id,
                ],
                [
                    'product_id'         => $product->id,
                    'attribute_id'       => $attribute->id,
                    'attribute_value_id' => $attributeValue->id,
                ]
            );
        }
    }

    /**
     * Get product title from row
     */
    private function getProductTitle($row)
    {
        // Try multiple column name variations
        $title = $row['product_title']
            ?? $row['Product Title']
            ?? $row['title']
            ?? $row['Title']
            ?? null;

        // If null, return null
        if ($title === null) {
            return null;
        }

        // Convert to string and trim
        $title = trim((string)$title);

        // Return null if empty after trim
        return $title === '' ? null : $title;
    }

    /**
     * Get product ID (SKU) from row
     */
    private function getProductId($row)
    {
        // Try multiple column name variations (normalized and original)
        $id = $row['product_id']
            ?? $row['Product ID']
            ?? $row['productid']
            ?? $row['product_id']  // Try with different case
            ?? null;

        // If null, return null
        if ($id === null) {
            return null;
        }

        // Convert to string and trim
        $id = trim((string)$id);

        // Return null if empty after trim, otherwise return the trimmed value
        return $id === '' ? null : $id;
    }

    /**
     * Get description from row
     */
    private function getDescription($row)
    {
        $desc = $row['detailed_description'] ?? null;
        return !empty($desc) ? trim($desc) : null;
    }

    /**
     * Get and clean price from row
     */
    private function getPrice($row)
    {
        $discountedPrice = $row['discounted_price']
            ?? $row['Discounted Price']
            ?? $row['discountedprice']
            ?? null;

        if (empty($discountedPrice)) {
            return null;
        }

        // Skip if it's a formula
        if (is_string($discountedPrice) && str_starts_with(trim($discountedPrice), '=')) {
            return null;
        }

        // Remove currency symbols (€, $, etc.), spaces, and commas
        $cleanedPrice = preg_replace('/[€$£,\s]/', '', (string)$discountedPrice);

        // Convert to float
        return is_numeric($cleanedPrice) ? (float) $cleanedPrice : null;
    }

    /**
     * ✅ Validation Rules
     * Validate required and optional columns
     */
    public function rules(): array
    {
        return [
            '*.product_title'  => 'nullable|string|max:255',
            '*.Product Title' => 'nullable|string|max:255',
            '*.product_id'     => 'nullable|string|max:255',
            '*.Product ID'     => 'nullable|string|max:255',
            '*.description'    => 'nullable|string',
            '*.discounted_price' => 'nullable|string',
            '*.Discounted Price' => 'nullable|string',
        ];
    }

    /**
     * ✅ Custom messages (optional)
     */
    public function customValidationMessages()
    {
        return [
            '*.product_title.string' => 'Product title must be a valid string.',
            '*.Product Title.string' => 'Product title must be a valid string.',
        ];
    }
}
