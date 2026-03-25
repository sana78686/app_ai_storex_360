<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Models\Tenant\InvoiceSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InvoiceSettingController extends Controller
{
    // 🔹 Get Invoice Settings (auto-create if not exists)
    public function show()
    {
        $settings = InvoiceSetting::firstOrCreate([], [
            'prefix' => 'INV',
            'next_number' => 1,
            'default_currency' => 'USD',
            'intro_text' => '',
            'footer_text' => '',
            'auto_generate_on_paid' => false,
        ]);

        return response()->json($settings);
    }

    // 🔹 Update Invoice Settings
    public function update(Request $request)
    {
        $settings = InvoiceSetting::firstOrCreate();

        $data = $request->validate([
            'prefix' => 'nullable|string|max:20',
            'next_number' => 'nullable|integer|min:1',
            'default_currency' => 'nullable|string|max:10',
            'intro_text' => 'nullable|string',
            'footer_text' => 'nullable|string',
            'auto_generate_on_paid' => 'nullable|boolean',

        ]);

        // 🔹 Logo upload (optional)


        $settings->update($data);

        return response()->json([
            'message' => 'Invoice settings updated successfully!',
            'data' => $settings
        ]);
    }
}
