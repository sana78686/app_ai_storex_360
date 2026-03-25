<?php

namespace App\Jobs;

use App\Imports\ProductsImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $filePath,
        protected string $tenantId,
        protected ?string $jobId = null
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update status to processing when job starts
            if ($this->jobId) {
                $this->setJobStatus('processing');
            }

            // Initialize tenancy context
            $tenant = \App\Models\Tenant::find($this->tenantId);

            if (!$tenant) {
                Log::error("Tenant not found: {$this->tenantId}");
                if ($this->jobId) {
                    $this->setJobStatus('failed');
                }
                throw new \Exception("Tenant not found: {$this->tenantId}");
            }

            // Run within tenant context
            $tenant->run(function () {
                // Check if file exists
                if (!Storage::disk('local')->exists($this->filePath)) {
                    Log::error("Import file not found: {$this->filePath}");
                    throw new \Exception("Import file not found: {$this->filePath}");
                }

                // Get full path to the file
                $fullPath = Storage::disk('local')->path($this->filePath);

                // Debug: Check Excel file directly using PhpSpreadsheet
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($fullPath);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                Log::info("Excel file analysis:");
                Log::info("  - Highest row number: {$highestRow}");
                Log::info("  - Highest column: {$highestColumn}");
                Log::info("  - Total rows (including header): " . ($highestRow));
                Log::info("  - Data rows (excluding header): " . ($highestRow - 1));

                // Check rows 2-10 to see what's happening
                for ($rowNum = 2; $rowNum <= min(10, $highestRow); $rowNum++) {
                    $productId = $sheet->getCell("B{$rowNum}")->getValue(); // Column B = product_id
                    $productTitle = $sheet->getCell("G{$rowNum}")->getValue(); // Column G = product_title
                    $isEmpty = empty($productId) && empty($productTitle);
                    Log::info("  - Row {$rowNum}: product_id='" . ($productId ?? 'NULL') . "', product_title='" . ($productTitle ?? 'NULL') . "', isEmpty=" . ($isEmpty ? 'YES' : 'NO'));
                }

                // Read Excel file directly using PhpSpreadsheet to get ALL rows
                // This bypasses Maatwebsite Excel's WithHeadingRow limitation
                $reader2 = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $reader2->setReadEmptyCells(true);
                $spreadsheet2 = $reader2->load($fullPath);
                $sheet2 = $spreadsheet2->getActiveSheet();
                $highestRow2 = $sheet2->getHighestRow();

                Log::info("Reading Excel file directly - Total rows: {$highestRow2}");

                // Helper function to normalize header names
                // This matches what Maatwebsite Excel does with WithHeadingRow
                $normalizeHeader = function($header) {
                    $header = trim($header);
                    // Convert to lowercase
                    $header = strtolower($header);
                    // Replace spaces and hyphens with underscores
                    $header = str_replace([' ', '-'], '_', $header);
                    // Remove special characters but keep underscores
                    $header = preg_replace('/[^a-z0-9_]/', '', $header);
                    return $header;
                };

                // Get headers from row 1 - read ALL columns
                $headers = [];
                $rawHeaders = [];

                // Convert highest column to column index
                $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Iterate through all columns properly
                for ($colIndex = 1; $colIndex <= $highestColIndex; $colIndex++) {
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $headerValue = $sheet2->getCell($col . '1')->getValue();

                    // Handle formula cells
                    if ($headerValue instanceof \PhpOffice\PhpSpreadsheet\Cell\Cell) {
                        $headerValue = $headerValue->getCalculatedValue();
                    }

                    $rawHeaders[$col] = $headerValue;

                    // Normalize header or use column position as fallback
                    if (!empty($headerValue) && trim((string)$headerValue) !== '') {
                        $normalized = $normalizeHeader((string)$headerValue);
                        $headers[$col] = $normalized;
                    } else {
                        // Use column position mapping for known columns
                        // Column B (index 2) = product_id (user confirmed)
                        if ($colIndex === 2) { // Column B
                            $headers[$col] = 'product_id';
                        } elseif ($colIndex === 7) { // Column G = product_title
                            $headers[$col] = 'product_title';
                        } elseif ($colIndex === 13) { // Column M = description
                            $headers[$col] = 'description';
                        } elseif ($colIndex === 9) { // Column I = discounted_price
                            $headers[$col] = 'discounted_price';
                        } else {
                            // For other empty headers, use column name as fallback
                            $headers[$col] = 'column_' . strtolower($col);
                        }
                    }
                }

                Log::info("Found " . count($headers) . " headers");
                Log::info("Sample headers (first 10): " . json_encode(array_slice($rawHeaders, 0, 10, true)));

                // Log key columns
                Log::info("Column B (product_id): header='" . ($rawHeaders['B'] ?? 'NULL') . "' -> mapped to: '" . ($headers['B'] ?? 'NOT_MAPPED') . "'");
                Log::info("Column G (product_title): header='" . ($rawHeaders['G'] ?? 'NULL') . "' -> mapped to: '" . ($headers['G'] ?? 'NOT_MAPPED') . "'");

                // Read all data rows (starting from row 2)
                $allRows = new \Illuminate\Support\Collection();
                for ($rowNum = 2; $rowNum <= $highestRow2; $rowNum++) {
                    $rowData = [];
                    foreach ($headers as $col => $headerName) {
                        $cellValue = $sheet2->getCell($col . $rowNum)->getValue();
                        // Handle formula cells - get calculated value
                        if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\Cell\Cell) {
                            $cellValue = $cellValue->getCalculatedValue();
                        }
                        $rowData[$headerName] = $cellValue;
                    }

                    // Debug first 5 rows to see what data we're getting
                    if ($rowNum <= 6) {
                        $productIdValue = $rowData['product_id'] ?? 'NOT_FOUND';
                        Log::info("  Row {$rowNum} - product_id value: " . (is_null($productIdValue) ? 'NULL' : "'{$productIdValue}'"));
                    }

                    // Only add row if it has at least one non-empty value
                    if (!empty(array_filter($rowData, fn($v) => !empty($v) && trim((string)$v) !== ''))) {
                        $allRows->push(new \Illuminate\Support\Collection($rowData));
                    }
                }

                Log::info("Processed {$allRows->count()} rows with data (out of " . ($highestRow2 - 1) . " total data rows)");

                // Debug: Show first row data structure
                if ($allRows->isNotEmpty()) {
                    $firstRowData = $allRows->first()->toArray();
                    Log::info("First row keys: " . implode(', ', array_keys($firstRowData)));
                    Log::info("First row product_id: " . ($firstRowData['product_id'] ?? 'NOT_FOUND'));
                }

                // Now process using ProductsImport's collection method
                $import = new ProductsImport();
                $import->processCollection($allRows);

                Log::info("Products imported successfully from: {$this->filePath}");
            });

            // Clean up: Delete the temporary file after import
            Storage::disk('local')->delete($this->filePath);
            Log::info("Temporary import file deleted: {$this->filePath}");

            // Update cache to mark job as completed (bypassing tenancy tags)
            if ($this->jobId) {
                $this->setJobStatus('completed');
            }

        } catch (\Exception $e) {
            Log::error("Product import job failed: " . $e->getMessage(), [
                'file_path' => $this->filePath,
                'tenant_id' => $this->tenantId,
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up file even on failure
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }

            // Update cache to mark job as failed (bypassing tenancy tags)
            if ($this->jobId) {
                $this->setJobStatus('failed');
            }

            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ImportProductsJob failed permanently", [
            'file_path' => $this->filePath,
            'tenant_id' => $this->tenantId,
            'error' => $exception->getMessage()
        ]);

        // Clean up file on permanent failure
        if (Storage::disk('local')->exists($this->filePath)) {
            Storage::disk('local')->delete($this->filePath);
        }

        // Update cache to mark job as failed (bypassing tenancy tags)
        if ($this->jobId) {
            $this->setJobStatus('failed');
        }
    }

    /**
     * Set job status in cache (bypassing tenancy tags)
     */
    private function setJobStatus(string $status): void
    {
        if (!$this->jobId) {
            return;
        }

        try {
            $cacheKey = config('cache.prefix') . "import_job_{$this->jobId}";
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
}

