<?php
namespace App\Http\Controllers\Api\v1\Tenant;
use App\Models\Tenant\MailSetting;
use App\Http\Requests\UpdateMailSettingsRequest;
use App\Http\Controllers\Controller;

class MailSettingsController extends Controller
{
    public function show()
    {
        return MailSetting::first() ?? new MailSetting();
    }

    public function update(UpdateMailSettingsRequest $request)
    {
        $settings = MailSetting::first() ?? new MailSetting();
        $settings->fill($request->validated());
        $settings->save();

        return response()->json([
            'message' => 'Mail settings updated successfully.',
            'data' => $settings
        ]);
    }

    public function test()
    {
        try {
            \Mail::raw("Mail Test Successful!", function ($m) {
                $m->to(auth()->user()->email)
                  ->subject("Mail Provider Test");
            });

            return ['success' => true, 'message' => 'Test email sent successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
