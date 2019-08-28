<?php

namespace App\Http\Controllers\Auth;

use App\Type;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('admin.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function register(Request $request)
    {
        if($request->isMethod('post')){
            $rules = [
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'login' => 'required|string|max:255|unique:users',
                'type' => 'required|string|max:25',
                'password' => 'required|string|min:6|confirmed'
            ];
            $this->validate($request,$rules);

            $this->create($request->all());

        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $type_id = Type::where('name',$data['type'])->first();
        return User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'login' => $data['login'],
            'password' => bcrypt($data['password'])
        ])->types()->attach($type_id->id);
    }
}
