<?php

namespace App\Http\Controllers\Api\v1\Central;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Tenant;
use App\Models\Tenant\Order;
use App\Http\Controllers\Controller;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Stripe recommended: validate webhook signature
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $signature,
                $webhookSecret
            );
        } catch (\Exception $e) {
            Log::error("Invalid Stripe Webhook Signature", [
                'error' => $e->getMessage()
            ]);
            return response('Invalid signature', 400);
        }

        // -----------------------------
        // 1. Identify connected tenant
        // -----------------------------
        $connectedAccountId = $event->account ?? null;

        if (!$connectedAccountId) {
            Log::info("Webhook ignored — event not related to any connected account");
            return response('ignored', 200);
        }

        $tenant = Tenant::where('stripe_account_id', $connectedAccountId)->first();

        if (!$tenant) {
            Log::error("Tenant not found for Stripe account", [
                'account' => $connectedAccountId
            ]);
            return response('Tenant not found', 404);
        }

        // Multi-tenant debugging
        Log::info("Stripe webhook received", [
            'tenant_id' => $tenant->id,
            'event_type' => $event->type
        ]);

        // -----------------------------
        // 2. Handle events
        // -----------------------------

        switch ($event->type) {

            // ---------------------------
            // Payment Intent (Card payments)
            // ---------------------------
            case 'payment_intent.succeeded':
                $pi = $event->data->object;

                $order = Order::where('payment_intent_id', $pi->id)->first();
                if ($order) {
                    $order->update(['status' => 'paid']);
                }

                Log::info("Payment successful", [
                    'tenant' => $tenant->id,
                    'payment_intent' => $pi->id
                ]);
                break;

            case 'payment_intent.payment_failed':
                $pi = $event->data->object;

                $order = Order::where('payment_intent_id', $pi->id)->first();
                if ($order) {
                    $order->update(['status' => 'failed']);
                }

                Log::warning("Payment failed", [
                    'tenant' => $tenant->id,
                    'payment_intent' => $pi->id
                ]);
                break;


            // -----------------------------------------
            // Checkout Session (recommended)
            // -----------------------------------------
            case 'checkout.session.completed':
                $session = $event->data->object;

                $order = Order::where('checkout_session_id', $session->id)->first();
                if ($order) {
                    $order->update([
                        'status' => 'paid',
                        'stripe_customer_id' => $session->customer,
                    ]);
                }

                Log::info("Checkout Session Paid", [
                    'tenant' => $tenant->id,
                    'session_id' => $session->id
                ]);

                break;



            default:
                Log::info("Unhandled Stripe webhook event", [
                    'event' => $event->type
                ]);
        }

        return response('Webhook processed', 200);
    }
}
