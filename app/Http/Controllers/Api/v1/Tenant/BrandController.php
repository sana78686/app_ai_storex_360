<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant\Brand;

class BrandController extends Controller
{
    // List all brands
    public function index()
    {
        $brands = Brand::all();
        return response()->json(['brands' => $brands]);
    }
}
