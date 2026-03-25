<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payment;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function webhook(Request $request)
    {
        $payment = Payment::where('transaction_id', $request->transaction_id)
            ->firstOrFail();

        app(PaymentGatewayService::class)
            ->confirm($payment, $request->all());

        return response()->json([
            'message' => 'Payment confirmed'
        ]);
    }
}
