<?php

namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Here you can add your email sending logic
        // For example:
        // Mail::to('your-email@example.com')->send(new ContactFormMail($request->all()));

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon!'
        ]);
    }
}
