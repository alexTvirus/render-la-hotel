<?php


namespace App\Services;
use App\Models\BaseModel as Model;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Storage;

class BaseServices
{
    public $model;


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function getCurrentUser(){
        return auth()->user();
    }

    protected function responseJson($message, $code = 200, $data=null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
