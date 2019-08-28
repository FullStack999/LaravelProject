<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $this->middleware('guest');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sendMail()
    {
        return view('home');
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        $user_check = User::where('email', $request->email)->first();
        if ($user_check == null) {
            return back()->withErrors(['Your account is not activated. Please activate it first.', 'email']);
        } else {
            if($user_check->types[0]->code == 0){
                try{
                    $response = $this->broker()->sendResetLink(
                        $request->only('email')
                    );
                }catch(\Exception $e){
                    return back()->withErrors(['Something Went Wrong', 'email']);
                }

                if ($response === Password::RESET_LINK_SENT) {
                    return back()->with('status', trans($response));
                }

                return back()->withErrors(
                    ['email' => trans($response)]
                );
            }else{
                return back()->withErrors(['You are not Admin. Only the Admin can Send Reset Password Link Request.', 'email']);
            }
        }
    }
}
