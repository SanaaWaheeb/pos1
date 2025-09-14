<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FreeProducts;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class FreePaymentController extends Controller
{
    /**
     * Send verification code to ensure each user will take one free sample
     */
    public function sendOtp(Request $request)
    {
        $type = $request->input('type'); // sms or email
    
        if ($type === 'sms') {
            $request->validate([
                'destination' => ['required', 'regex:/^\+?[1-9]\d{6,15}$/']
            ]);
        } else {
            $request->validate([
                'destination' => 'required|email'
            ]);
        }
        $to = $request->input('destination');

        if ($type === 'sms') {
            // Send verification code via SMS
            $twilioSid = env('ACCOUNT_SID');
            $twilioToken = env('AUTH_TOKEN_TWILIO');
            $serviceId = env('SERVICE_ID');
            $client = new Client($twilioSid, $twilioToken);
            try {
                $client->verify->v2->services($serviceId)
                    ->verifications
                    ->create($to, "sms");
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

        } else {
            // Send verification code via email
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $to, $otp, now()->addMinutes(5));
            Mail::raw("Your verification code is: $otp", function ($message) use ($to) {
                $message->to($to)->subject('Your Verification Code');
            });
        }
        return response()->json(['success' => true]);
    }

    /**
     * Verify the entered verification code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'destination' => 'required',
            'code' => 'required'
        ]);

        $to = $request->input('destination');
        $code = $request->input('code');
        $productId = $request->input('productId');

        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            // Verify code sent by email
            $stored = Cache::get('otp_' . $to);
            $valid = $stored && $stored == $code;
        } else {
            // SMS verification via Twilio Verify
            Log::debug("Check validity for phone");
            $twilioSid = env('ACCOUNT_SID');
            $twilioToken = env('AUTH_TOKEN_TWILIO');
            $serviceId = env('SERVICE_ID');

            $client = new Client($twilioSid, $twilioToken);
            $check = $client->verify->v2->services($serviceId)
                ->verificationChecks
                ->create([
                    'to' => $to,
                    'code' => $code
                ]);
            $valid = $check->status === 'approved';
        }

        if (!$valid) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired code. Please try again'
            ]);
        }

        // Verification passed, now check free product eligibility
        $eligibility = $this->checkValidity($to, $productId);

        if (!$eligibility) {
            return response()->json([
                'valid' => true,
                'eligible' => false,
                'message' => 'Free product has reached the limit'
            ]);
        }

        return response()->json(['valid' => true, 'eligible' => true]);
    }

    /**
     * Make sure the customer will not take more than one free sample
     */
    private function checkValidity(String $destination, int $productId)
    {
        // Make sure the product id exist
        Product::findOrFail($productId);

        // Search for previous payment for this (product & user)
        $data = [ 'product_id' => $productId ];
        $query = FreeProducts::where('product_id', $productId);
        if (filter_var($destination, FILTER_VALIDATE_EMAIL)) {
            $query->where('email', $destination);
            $data['email'] = $destination;
        } else {
            Log::debug("Check eligibility for phone");
            $query->where('phone', $destination);
            $data['phone'] = $destination;
        }

        $alreadyClaimed = $query->exists();
        if (!$alreadyClaimed) {
            FreeProducts::create($data);
        }

        Log::debug("Eligible: " . !$alreadyClaimed);
        return !$alreadyClaimed; // true means eligible
    }
}
