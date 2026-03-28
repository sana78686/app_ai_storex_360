<?php
namespace App\Http\Controllers\Api\v1\Tenant;
use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stancl\Tenancy\Tenant;
use App\Models\Tenant\Product;
use App\Models\Tenant\Order;
use App\Models\Tenant\Invoice;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoicePDFService;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\Tenant\InvoiceSetting;
use App\Models\Tenant\Media;
use App\Services\MediaUploadService;
use App\Support\TenantUrl;
class CheckoutController extends Controller
{
    public function createSession(Request $request)
    {
        $tenant = tenant(); // get current tenant
        $tenantDomain = TenantUrl::to(tenant('domains')->first()->domain);
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

       $amount = $request->final_total ?? ($product->price * $request->quantity);
Stripe::setApiKey(Crypt::decryptString($tenant->stripe_access_token));
$session = Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $product->name,
            ],
            'unit_amount' => $amount * 100,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    // 'success_url' => config('app.url') . '/stripe/success?session_id={CHECKOUT_SESSION_ID}',
    // 'cancel_url'  => config('app.url') . '/stripe/cancel',
        'success_url' => $tenantDomain . "/product/{$product->id}?session_id={CHECKOUT_SESSION_ID}",
'cancel_url'  => $tenantDomain . "/product/{$product->id}?cancel=true",

]);

        // Optionally: create a pending order in tenant DB
        $orderNumber = 'ORD-' . strtoupper(uniqid());

$order = Order::create([
    'order_number' => $orderNumber,
    'product_id' => $product->id,
    'quantity' => $request->quantity,
    'order_status' => 'pending', // use order_status instead of status
    'stripe_session_id' => $session->id,
    'payment_status' => 'pending',
     'discount' => $request->discount ?? 0,
      'coupon_code' => $request->coupon['code'] ?? null,
    'subtotal' => $product->price * $request->quantity,
    'total' => $product->price * $request->quantity, // add tax/discount later
]);


        return response()->json([
            'checkout_url' => $session->url,
        ]);
    }


public function verifyCheckoutSession(Request $request)
{
    $sessionId = $request->query('session_id');

    if (!$sessionId) {
        return response()->json([
            'success' => false,
            'message' => 'Missing session_id'
        ], 400);
    }

    $tenant = tenant();
    Stripe::setApiKey(Crypt::decryptString($tenant->stripe_access_token));

    try {
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Stripe session fetch failed',
            'error'   => $e->getMessage()
        ], 500);
    }

    if ($session->payment_status !== 'paid') {
        return response()->json([
            'success' => false,
            'message' => 'Payment is not completed yet.'
        ]);
    }

    $order = Order::where('stripe_session_id', $sessionId)->first();

    if (!$order) {
        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }

    $customerEmail = $session->customer_details['email'] ?? null;

    $order->update([
        'customer_email' => $customerEmail,
        'order_status'   => 'completed',
        'payment_status' => 'paid',
    ]);

    $settings = InvoiceSetting::first();

    $invoice = Invoice::create([
        'invoice_number' => InvoiceNumberGenerator::generate(),
        'order_id'       => $order->id,
        'subtotal'       => $order->subtotal,
        'tax'            => 0,
        'total'          => $order->total,
        'currency'       => $settings->default_currency,
        'customer_email' => $customerEmail,
        'intro_text'     => $settings->intro_text,
        'footer_text'    => $settings->footer_text
    ]);

    // ✅ Generate PDF
    $product = Product::find($order->product_id);
    $pdf = InvoicePDFService::generate($invoice, $order, $product, $settings);

    // ✅ Upload to Spaces
    $mediaService = new MediaUploadService();

    $upload = $mediaService->uploadRaw(
        $pdf,
        $invoice->invoice_number . '.pdf',
        'invoice',
        $invoice->id
    );

    // ✅ Create media record (linked to invoice)
    $media = $invoice->media()->create([
        'type'      => 'invoice',
        'entity_id' => $invoice->id,
        'file_key'  => $upload['file_key'],
        'cdn_url'   => $upload['cdn_url'],
        'mime_type'=> 'application/pdf',
        'size'      => strlen($pdf),
    ]);

    // ✅ Send email
    if ($customerEmail) {
       Mail::to($customerEmail)->send(
    new InvoiceMail(
        $invoice,
        $media->cdn_url,              // 👈 S3 PDF link
        url("/orders/" . $order->id)
    )
);

    }

    return response()->json([
        'success' => true,
        'message' => 'Payment verified, invoice stored & emailed',
        'order'   => $order,
        'invoice' => [
            'id'          => $invoice->id,
            'number'      => $invoice->invoice_number,
            'pdf_url'     => $media->cdn_url,
        ]
    ]);
}



}
