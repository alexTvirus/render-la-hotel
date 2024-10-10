<?php

namespace App\Http\Controllers\ApiControllers\V1\Auth;

use App\Http\Controllers\BaseController;
use App\Mails\ResetPasswordMail;
use App\Services\UserServices;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use App\Mails\VerifyMail;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword']]);
        $this->userServices = $userServices;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'phone' => 'required|string|max:20|between:6,20',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorJson("fail", Response::HTTP_CONFLICT, $validator->errors()->first());
        }

        $validatorArray = $validator->validated();
        $user = $this->userServices->save(array_merge(
            $validatorArray,
            ['password' => bcrypt($request->password)]
        ));

        $token = auth()->attempt($validatorArray);

        $verificationUrl = $this->genUrlVerify($token);

        Mail::to($user->email)->send(new VerifyMail($verificationUrl));

        return $this->responseJson('User successfully registered', Response::HTTP_OK, $user);
    }


    public function login(Request $request)
    {
        $attributes = $request->only('email', 'password');

        foreach ($attributes as &$value) {
            $value = trim($value);
        }
        $validator = Validator::make($attributes, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorJson("fail", 422, $validator->errors()->first());
        }
        $credentials = $validator->validated();
        if (!$token = auth()->attempt(['email' => $request->email, 'password' => $request->password, 'isActive' => 1])) {
            return $this->responseErrorJson("fail", 422, 'email hoặc password sai');
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            return $this->responseErrorJson("fail", 422, 'User chưa được xác thực');
        }
        return $this->respondWithToken($token, $credentials);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->responseJson('success', Response::HTTP_OK, [
            'userProfile' => $this->userProfile()
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'old_password' => 'required|string|min:6|current_password:api',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorJson("fail", Response::HTTP_CONFLICT, $validator->errors()->first());
        }

        $user = auth()->user();
		
        if ($this->userServices->save(['id' => $user->id, 'password' => bcrypt($request->password)]))
            return $this->respondWithToken(auth()->refresh());
        return $this->responseErrorJson('fail', Response::HTTP_UNPROCESSABLE_ENTITY, "'Đổi mật khẩu thất bại'");
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->get("email") ?? "";
        $user = $this->userServices->getUserByEmail($email);
        if (!$user) {
            return $this->responseErrorJson('fail', Response::HTTP_CONFLICT, "'User does not exist.'");
        }
        $password = $this->genRandomPassword();
        $this->userServices->save(['id' => $user->id, 'password' => bcrypt($password)]);
        Mail::to($user->email)->send(new ResetPasswordMail($password, $email));
        return $this->responseJson('success', Response::HTTP_OK, "'Hãy kiểm tra email'");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $u = auth()->user();
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $credentials = null)
    {
        $token = $credentials ? auth()->attempt($credentials) : $token;
        $userProfile = $this->userProfile();
        return $this->responseJson('success', Response::HTTP_OK, [
            'userProfile' => $userProfile,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    private function userProfile()
    {
        $user = auth()->user();
        $user->wishlists;
        return $user;
    }
}
