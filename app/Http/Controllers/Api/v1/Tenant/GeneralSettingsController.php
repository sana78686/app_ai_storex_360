<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Models\Tenant\GeneralSetting;
use App\Http\Requests\UpdateGeneralSettingsRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\MediaUploadService;
class GeneralSettingsController extends Controller
{
    public function show()
    {
        $defaultTheme = config('themes.storefront.default', 'prism');
        $settings = GeneralSetting::firstOrCreate([], [
            'theme' => $defaultTheme,
        ]);
        return response()->json($settings);
    }

public function update(UpdateGeneralSettingsRequest $request)
{
    $settings = GeneralSetting::first() ?? new GeneralSetting();

    $data = $request->validated();

    // Delete old logo file if exists (only if you stored locally before)
    if ($request->hasFile('logo') && $settings->logo && Storage::exists($settings->logo)) {
        Storage::delete($settings->logo);
    }

    // Use media service for uploading logo
    if ($request->hasFile('logo')) {

        $mediaService = new MediaUploadService();
        $file = $request->file('logo');

        // Upload file
        $upload = $mediaService->upload($file, 'logo', $settings->id);

        // Delete old media record (optional)
        $settings->media()->where('type', 'logo')->delete();

        // Create new media record
        $settings->media()->create([
            'type'      => 'logo',
            'entity_id' => $settings->id,
            'file_key'  => $upload['file_key'],
            'cdn_url'   => $upload['cdn_url'],
            'mime_type' => $upload['mime'],
            'size'      => $upload['size'],
        ]);

        // Save file_key as logo column if needed
        $data['logo'] = $upload['cdn_url']; // or file_key
    }

    $settings->fill($data)->save();

    return response()->json([
        'message' => 'General settings updated successfully!',
        'data'    => $settings
    ]);
}


}
