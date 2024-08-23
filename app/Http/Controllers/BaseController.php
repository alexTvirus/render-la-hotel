<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct()
    {
    }

    protected function responseJson($message, $code = 200, $data=null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function responseSuccessMobileJson($message, $code = 200, $data=null)
    {
        return response()->json([
            'code' => $code,
            'message_mobile' => $message,
            'error_mobiles' => []
        ], $code);
    }

    protected function responseErrorJson($message, $errors, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    protected function responseErrorMobileJson($message, $errors, $code = 200)
    {
        return response()->json([
            'code' => $code,
            'message_mobile' => $message,
            'error_mobiles' => $errors
        ], $code);
    }
}
