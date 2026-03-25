<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Models\Tenant\LegalInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LegalInfoController extends Controller
{
    public function index()
    {
        return LegalInfo::all();
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'company_name' => 'required',
        'legal_form' => 'required',
        'registered_address' => 'required',
        'authorized_representative' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'registration_court' => 'required',
        'commercial_register_number' => 'required',
        'vat_id' => 'nullable',
        'oss' => 'boolean'
    ]);

    // Fetch first OR create new
    $legalInfo = LegalInfo::first() ?? new LegalInfo();

    // Fill + save
    $legalInfo->fill($validated);
    $legalInfo->save();

    return response()->json([
        'message' => 'Saved successfully',
        'data' => $legalInfo
    ]);
}



}

