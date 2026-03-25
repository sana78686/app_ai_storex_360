<?php

namespace App\Services;

use App\Models\Tenant\GeneralSetting;
use App\Models\Tenant\InvoiceTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class InvoicePDFService
{
    public static function generate($invoice, $order, $product)
    {
        $settings = GeneralSetting::first();
        $logoPath = null;

        // TEMP LOGO IMPORT
        if ($settings?->logo) {
            $imageContents = file_get_contents($settings->logo);
            $tempPath = storage_path('app/tmp/' . Str::uuid() . '.jpg');

            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            file_put_contents($tempPath, $imageContents);
            $logoPath = $tempPath;
        }

        // Load template
        $template = InvoiceTemplate::where('is_default', true)
                                   ->first();

        // Fallback default view if no custom template exists
        if (!$template) {
            return Pdf::loadView('invoices.default', [
                'invoice'  => $invoice,
                'order'    => $order,
                'product'  => $product,
                'settings' => $settings,
                'logoPath' => $logoPath,
            ])->output();
        }

        // RENDER CUSTOM TEMPLATE
        return Pdf::loadView('invoices.invoice', [
            'template' => $template,
            'invoice'  => $invoice,
            'order'    => $order,
            'product'  => $product,
            'settings' => $settings,
            'logoPath' => $logoPath,
        ])->output();
    }
}
