<?php

namespace App\Http\Controllers\Auth;

use App\Events\VerifyReCaptchaToken;
use App\Http\Controllers\Controller;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $settings = Utility::settings();
        $validation=[];
        if(isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes')
        {
            if($settings['google_recaptcha_version'] == 'v2'){
                $validation['g-recaptcha-response'] = 'required';
            } elseif ($settings['google_recaptcha_version'] == 'v3'){
                $result = event(new VerifyReCaptchaToken($request));
                if (!isset($result[0]['status']) || $result[0]['status'] != true) {
                    $key = 'g-recaptcha-response';
                    $request->merge([$key => null]);
                    $validation['g-recaptcha-response'] = 'required';
                }
            }
        }
         else
        {
            $validation=[];
        }
        $this->validate($request, $validation);
        
        $request->validate([
            'email' => 'required|email',
        ]);
        Utility::getSMTPDetails(1);
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        try{

                $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                        ? back()->with('status', __($status))
                        : back()->withInput($request->only('email'))
                                ->withErrors(['email' => __($status)]);
        }
        catch(\Exception $e)
        {

            return redirect()->back()->with('status',__('E-Mail has been not sent due to SMTP configuration'));
            
        }
        
    }
}
