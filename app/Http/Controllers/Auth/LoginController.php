<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Where to redirect users after login.
     */
    protected function redirectTo()
    {
        if(Auth::check()) {
            switch (Auth::user()->type_id){
                case 1: return $this->redirectTo = 'admin'; break;
                case 2: return $this->redirectTo = 'user1'; break;
                case 3: return $this->redirectTo = 'user2'; break;
                case 4: return $this->redirectTo = 'user3'; break;
                case 5: return $this->redirectTo = 'user4'; break;
                case 6: return $this->redirectTo = 'user5'; break;
                case 7: return $this->redirectTo = 'user6'; break;
                case 8: return $this->redirectTo = 'user7'; break;
            }
        }
        $this->guard()->logout();
            return $this->redirectTo = 'login';
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request){

        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('login');
    }

    /**
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username() : 'login';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    /**
     * @param Request $request
     * @return $this
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => 'Մուտքանունը կամ գաղտնաբառը սխալ է'];
        return redirect()->back()
            ->withInput($request->only($this->username()))
            ->withErrors($errors);
    }
}
