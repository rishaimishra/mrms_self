<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    public function showLinkRequestForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email|exists:admin_users']);

        $adminUser = AdminUser::where('email', $request->email)->first();


        if(!$adminUser || $adminUser->hasRole('cashiers'))
        {
            return redirect()->back()->with($this->setMessage('You can not reset your password.', self::MESSAGE_SUCCESS));

        }


        $response  = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with($this->setMessage('!Hi, We have sent you a reset link to your given email.', self::MESSAGE_SUCCESS));

            case Password::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function broker(){
        return Password::broker('admins');
    }

    protected function getEmailSubject()
    {
        return isset($this->subject) ? $this->subject : 'Your Password Reset Link';
    }

    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('auth.reset')->with('token', $token);
    }
}
