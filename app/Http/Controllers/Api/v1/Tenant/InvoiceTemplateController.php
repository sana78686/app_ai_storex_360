<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\InvoiceTemplate;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use App\Models\Tenant\GeneralSetting;
class InvoiceTemplateController extends Controller
{
    public function index()
    {
        return InvoiceTemplate::get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'template_html' => 'required|string',
            'template_css'  => 'nullable|string', // NO PURIFIER
            'settings'      => 'nullable|array',
            'is_default'    => 'boolean',
        ]);

        // Sanitize HTML ONLY — NO <style> allowed
        $data['template_html'] = $this->sanitizeHtml($data['template_html']);

        // if (!empty($data['is_default'])) {
        //     InvoiceTemplate::where('tenant_id', tenant('id'))
        //         ->update(['is_default' => false]);
        // }

        // $data['tenant_id'] = tenant('id');

        return InvoiceTemplate::create($data);
    }

    public function update(Request $request, InvoiceTemplate $template)
    {
        abort_if($template->tenant_id !== tenant('id'), 403);

        $data = $request->validate([
            'name'          => 'string',
            'template_html' => 'string',
            'template_css'  => 'nullable|string', // DO NOT PURIFY CSS
            'settings'      => 'nullable|array',
            'is_default'    => 'boolean',
        ]);

        if (isset($data['template_html'])) {
            $data['template_html'] = $this->sanitizeHtml($data['template_html']);
        }

        if (!empty($data['is_default'])) {
            InvoiceTemplate::where('tenant_id', tenant('id'))
                ->update(['is_default' => false]);
        }

        $template->update($data);

        return $template;
    }

    protected function sanitizeHtml(string $html): string
    {
        // REMOVE <style> completely before purification
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $html);

        return Purifier::clean($html, [
            'HTML.Allowed' =>
                'div,h1,h2,h3,p,br,strong,em,table,thead,tbody,tr,td,th,ul,ol,li,span',
            'AutoFormat.RemoveEmpty' => false,
        ]);
    }



// use Illuminate\Support\Facades\Blade;




// use App\Models\Tenant\InvoiceTemplate;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\Blade;
// use Illuminate\Support\Str;



public function preview(Request $request)
{
    // 🚨 Block dangerous Blade directives
    $forbidden = ['@php', '@dd', '@dump', '@die', '@exit'];
    foreach ($forbidden as $needle) {
        if (str_contains($request->template_html, $needle)) {
            abort(403, 'Forbidden Blade directive used');
        }
    }

    // 🔹 Fake company settings
    $settings = (object) [
        'company_name' => 'Demo Company GmbH',
        'street'       => 'Alexanderplatz 1',
        'zip'          => '10178',
        'city'         => 'Berlin',
        'country'      => 'Germany',
        'email'        => 'info@demo-company.com',
        'phone'        => '+49 30 1234567',
        'logo'         => null, // optional URL or base64
    ];

    // 🔹 Fake invoice object including items
    $invoice = (object) [
        'invoice_number' => 'INV-0001',
        'customer_name'  => 'John Doe',
        'customer_email' => 'customer@example.com',
        'subtotal'       => 120.00,
        'tax'            => 20.00,
        'total'          => 140.00,
        'currency'       => 'EUR',
        'created_at'     => Carbon::now(),
        'due_date'       => Carbon::now()->addDays(14),
        'items'          => [
            (object)[
                'description' => 'Test Product 1',
                'quantity'    => 2,
                'unit_price'  => 30.00,
                'total'       => 60.00,
            ],
            (object)[
                'description' => 'Test Product 2',
                'quantity'    => 1,
                'unit_price'  => 60.00,
                'total'       => 60.00,
            ],
        ],
    ];

    // 🔹 Optional default template (fake data preview)
    $template = (object) [
        'title'       => 'Demo Invoice Template',
        'is_default'  => true,
        'template_html' => $request->template_html,
        'template_css'  => $request->template_css,
    ];

    // 🔹 Render HTML for preview
    $html = Blade::render(
        '<style>'.$request->template_css.'</style>'.$request->template_html,
        [
            'settings'     => $settings,
            'invoice'      => $invoice,
            'invoice_date' => now()->format('d.m.Y'),
            'due_date'     => now()->addDays(14)->format('d.m.Y'),
            'template'     => $template,
        ]
    );

    return response()->json([
        'html' => $html,
    ]);
}





}
