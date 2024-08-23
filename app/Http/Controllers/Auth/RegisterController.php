<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\WebControllers\V1\Backend\LandingController;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
//use App\Http\Controllers\Auth\RegisterController;

class RegisterController extends BaseController
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


    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        parent::__construct();
    }

    public function getRegister()
    {
        if (Auth::guard()->check()) {
            return redirect()->intended(action([LandingController::class,'index']));
        }

        return view('Backend.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->only([
            'name',
            'email',
            'password'
        ]);

        $input = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ];
        $user = $this->create($input);
        if ($user) {
            Auth::login($user);
            return redirect()->intended(action([LandingController::class,'index']));
        }

        return redirect()
            ->action([RegisterController::class,'getRegister'])
            ->with('message', trans('message.register_fail'));

    }
    protected function create( $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
