<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    /**
     * Register a new customer (storefront).
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $nameParts = preg_split('/\s+/', trim($request->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        $customer = Customer::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'type' => 'registered',
        ]);

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'customer' => $this->customerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login customer (storefront).
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Only allow registered customers to log in
        if ($customer->type !== 'registered') {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $customer->tokens()->delete();
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'customer' => $this->customerResource($customer),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get authenticated customer (requires auth:customer).
     */
    public function me(Request $request): JsonResponse
    {
        $customer = $request->user('customer');
        if (! $customer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json(['customer' => $this->customerResource($customer)]);
    }

    /**
     * Logout customer (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user('customer')->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    private function customerResource(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'email' => $customer->email,
            'name' => $customer->name,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone' => $customer->phone,
        ];
    }
}
