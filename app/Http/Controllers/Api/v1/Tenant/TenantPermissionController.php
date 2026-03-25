<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\tenant\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantPermissionController extends Controller
{
    /**
     * Display a paginated list of permissions
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response {
     *  "status": true,
     *  "message": "Permissions retrieved successfully",
     *  "data": {
     *    "current_page": 1,
     *    "data": [
     *      {
     *        "id": 1,
     *        "name": "view_users"
     *      }
     *    ],
     *    "total": 1
     *  }
     * }
     */
    public function index()
    {
        try {
            $permissions = Permission::paginate(10);

            if ($permissions->total() > 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'Permissions retrieved successfully',
                    'data' => $permissions
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'There are no permissions',
                    'data' => $permissions
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Store a newly created permission
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam name string required The name of the permission. Example: edit_users
     *
     * @response {
     *  "status": true,
     *  "message": "Permission created successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "edit_users"
     *  }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permission = Permission::create(['name' => $request->name]);

            return response()->json([
                'status' => true,
                'message' => 'Permission created successfully',
                'data' => $permission
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified permission
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @response {
     *  "status": true,
     *  "message": "Permission retrieved successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "edit_users"
     *  }
     * }
     */
    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Permission retrieved successfully',
                'data' => $permission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified permission
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam name string required The name of the permission. Example: edit_users
     *
     * @response {
     *  "status": true,
     *  "message": "Permission updated successfully",
     *  "data": {
     *    "id": 1,
     *    "name": "edit_users"
     *  }
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:permissions,name,' . $id
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $permission = Permission::findOrFail($id);
            $permission->update(['name' => $request->name]);

            return response()->json([
                'status' => true,
                'message' => 'Permission updated successfully',
                'data' => $permission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified permission
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @response {
     *  "status": true,
     *  "message": "Permission deleted successfully"
     * }
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'status' => true,
                'message' => 'Permission deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting permission',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
