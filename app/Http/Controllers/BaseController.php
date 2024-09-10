<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
    }

    private $seed = 'abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&';

    protected function genRandomPassword()
    {
        $random = str_shuffle($this->seed);
        return substr($random, 0, 12);
    }

    protected function genUrlVerify($token)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['token' => $token]
        );
    }

    protected function responseJson($message, $code = 200, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function responseSuccessMobileJson($message, $code = 200, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message_mobile' => $message,
            'error_mobiles' => []
        ], $code);
    }

    protected function responseErrorJson($message, $code = 200, $errors)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    protected function responseErrorMobileJson($message, $code = 200, $errors)
    {
        return response()->json([
            'code' => $code,
            'message_mobile' => $message,
            'error_mobiles' => $errors
        ], $code);
    }
}
