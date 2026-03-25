<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    /**
     * Generate unique invoice number (safe & atomic)
     */
    public static function generate($tenantId = null)
    {
        return DB::transaction(function () use ($tenantId) {

            // Lock invoices table (avoids duplicate numbers)
            $query = DB::table('invoices')->lockForUpdate();

            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            }

            // Get last invoice number
            $lastInvoice = $query->orderBy('id', 'desc')->first();

            $nextNumber = 1;

            if ($lastInvoice && $lastInvoice->invoice_number) {

                // Extract numeric part
                preg_match('/\d+$/', $lastInvoice->invoice_number, $matches);

                if (!empty($matches)) {
                    $nextNumber = intval($matches[0]) + 1;
                }
            }
// use it later
//             $settings = InvoiceSetting::first();

// $invoiceNumber = $settings->prefix . '-' . str_pad($settings->next_number, 6, '0', STR_PAD_LEFT);

// // increment for next invoice
// $settings->increment('next_number');

            // Format: INV-000001
            $prefix = "INV-" . date('Y') . "-";
            return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // return "INV-" . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        });
    }
}
