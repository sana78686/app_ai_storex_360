<?php

namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;

class CentralUserController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Signup(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [

                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation failed',
                'error' => $validateUser->errors()->all(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'status' => true,
            'message' => 'signup successfully',
            'central_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    /**
     * Authenticate a user and generate access token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Login(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication failed',
                'error' => $validateUser->errors()->all(),
            ], 422);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status' => false,
                'message' => 'Email or Password not matched',
            ], 401);
        }

        $authUser = Auth::user();
        $token = $authUser->createToken('auth_token')->plainTextToken;
        $authUser->load('roles');

        return response()->json([
            'status' => true,
            'message' => 'You are logged in successfully',
            'user' => $authUser,
            'central_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /**
     * Logout the authenticated user and revoke all tokens
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Revoke the token that was used to authenticate the current request
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error logging out',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if the current token is valid
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request) {
        $user = Auth::user();
        if ($user && $user->tokens()->exists()) {
            return response()->json(['valid' => true], 200);
        }
        return response()->json(['valid' => false], 401);
    }

    /**
     * Update the specified user's details
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'email' => 'nullable|email|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
                'roles' => 'required',
                'roles.*' => 'exists:roles,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);

            // Update user details
            $updateData = $request->only(['name', 'email']);
            if ($request->has('password')) {
                $updateData['password'] = bcrypt($request->password);
            }
            $user->update($updateData);

            // Update roles
            $roles = is_array($request->roles) ? $request->roles : [$request->roles];
            $user->syncRoles($roles);

            // Refresh the user model to get the updated roles
            $user->refresh();

            // Load the roles relationship
            $user->load('roles');

            return response()->json([
                'status' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating user:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $users = User::with('roles')->get();
            return response()->json([
                'status' => true,
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRoles()
    {
        try {
            $roles = \Spatie\Permission\Models\Role::all();
            return response()->json([
                'status' => true,
                'data' => $roles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
