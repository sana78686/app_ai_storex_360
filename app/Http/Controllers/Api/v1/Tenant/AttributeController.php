<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Attribute;

class AttributeController extends Controller
{
    // List all attributes
    public function index()
    {
        return response()->json(Attribute::with('values')->get());
    }

    // Store a new attribute
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $attribute = Attribute::create($data);
        return response()->json($attribute, 201);
    }

    // Show a single attribute
    public function show($id)
    {
        $attribute = Attribute::with('values')->findOrFail($id);
        return response()->json($attribute);
    }

    // Update an attribute
    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $attribute->update($data);
        return response()->json($attribute);
    }

    // Delete an attribute
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();
        return response()->json(['message' => 'Attribute deleted']);
    }
}
