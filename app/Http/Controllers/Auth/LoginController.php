<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\WebControllers\V1\Backend\LandingController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends BaseController
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


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        parent::__construct();
    }

    public function logout(){
        auth()->logout();
        return view('Backend.auth.login');
    }

    public function getLogin()
    {
        if (Auth::guard()->check()) {
            return back();
        }
        return view('Backend.auth.login');
    }

    public function login(LoginRequest $request)
    {

        $remember = $request->get('remember');

        $data = $request->only([
            'email',
            'password',
        ]);

        if (Auth::attempt(
            [
                'email' => $data['email'],
                'password' => $data['password'],
            ], $remember)
        ) {

            return redirect()->intended(action([LandingController::class,'index']));
        }
        return redirect()
            ->action('Auth\LoginController@getLogin')
            ->with('msg_fail', trans('auth.failed'));
    }
}
