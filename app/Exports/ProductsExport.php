<?php

namespace App\Exports;

use App\Models\Tenant\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Load all relations for export
        return Product::with(['images', 'variants.values.option', 'categories', 'infoFields', 'tags'])
            ->select('id', 'name', 'sku', 'price', 'stock', 'status')
            ->get();
    }

    /**
     * Map each product row into Excel-friendly format
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->sku,
            $product->price,
            $product->stock,
            $product->status,

            // ✅ Relations converted to strings
            implode("\n", $product->images->pluck('url')->toArray()),
            // $product->images->pluck('url')->implode(', '), // Images URLs
            $product->variants->map(function ($variant) {
                $values = $variant->values->map(function ($value) {
                    return $value->option->name . ':' . $value->value;
                })->implode('|');
                return $variant->name . ' [' . $values . ']';
            })->implode('; '),

            $product->categories->pluck('name')->implode(', '), // Categories
            $product->tags->pluck('name')->implode(', '),       // Tags
          implode("\n", $product->infoFields->map(fn($field) => $field->name . ':' . $field->value)),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Price',
            'Stock',
            'Status',
            'Images',
            'Variants',
            'Categories',
            'Tags',
            'Info Fields',
        ];
    }

      // 👇 Set column widths
    public function columnWidths(): array
    {
        return [
            'A' => 10,   // ID
            'B' => 25,   // Name
            'C' => 20,   // SKU
            'D' => 15,   // Price
            'E' => 15,   // Stock
            'F' => 15,   // Status
            'G' => 400,  // Images column (approx 400px)
        ];
    }
}
