<?php


namespace App\Http\Controllers\ApiControllers\V1\Auth;


use App\Http\Controllers\BaseController;
use App\Mails\VerifyMail;
use App\Services\UserServices;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificationController extends BaseController
{
    private $userServices;

    /**
     * Instantiate a new VerificationController instance.
     */
    public function __construct(UserServices $userServices)
    {
//        $this->middleware('auth');
//        $this->middleware('signed')->only('verify');
//        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->userServices = $userServices;
    }

    /**
     * Display an email verification notice.
     *
     * @return \Illuminate\Http\Response
     */
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ?
            $this->responseErrorJson('fail', Response::HTTP_FAILED_DEPENDENCY, 'User fail verified')
            :
            $this->responseJson("sucess", Response::HTTP_OK, 'User successfully verified');
    }

    /**
     * User's email verificaiton.
     *
     * @param \Illuminate\Http\EmailVerificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $token = $request->query('token')??"";

        $user = JWTAuth::setToken($token)->authenticate();

        if (!$request->hasValidSignature()) {
            $this->responseErrorJson("fail", Response::HTTP_BAD_REQUEST, 'Invalid or expired link');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
            return redirect('http://localhost:3000/login');
        }

        return $this->responseJson('fail', Response::HTTP_BAD_REQUEST, 'User is incorrect');
    }

    /**
     * Resent verificaiton email to user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user = $this->userServices->getUserByEmail($request->email);
        if (!$user) {
            return $this->responseErrorJson('fail', Response::HTTP_OK, "'User does not exist.'");
        }
        $token = JWTAuth::fromUser($user);
        $verificationUrl = $this->genUrlVerify($token);
        Mail::to($user->email)->send(new VerifyMail($verificationUrl));

        return $this->responseJson('success', Response::HTTP_OK, "'A fresh verification link has been sent to your email address.'");

    }

}
