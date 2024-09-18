<?php


namespace App\Services;

use App\Models\BaseModel;
use App\Models\BaseModel as Model;
use Carbon\Carbon;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Storage;

class BaseServices
{
    public $model;


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function getCurrentUser()
    {
        return auth()->user();
    }

    protected function timeCondition($request,&$query, $tableName)
    {
        $query_array = $request->query();
        $month = $query_array['month'] ?? "";
        $year = $query_array['year'] ?? "";
        $quarter = $query_array['quarter'] ?? "";

        if (!empty($month) && !empty($year)) {
            $query = $query->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $month);
        }

        if (empty($month) && !empty($year)) {
            $query = $query->whereYear('updated_at', $year);
        }

        if (!empty($quarter) && !empty($year)) {
            $start_date = Carbon::create()->year($year)->month(BaseModel::QUATER_OF_YEAR[$quarter])->firstOfQuarter()->format('Y-m-d');
            $end_date = Carbon::create()->year($year)->month(BaseModel::QUATER_OF_YEAR[$quarter])->endOfQuarter()->format('Y-m-d');
            $query = $query
                ->whereRaw($tableName.".updated_at <= STR_TO_DATE(?, '%Y-%m-%d')", $end_date)
                ->whereRaw($tableName.".updated_at >= STR_TO_DATE(?, '%Y-%m-%d')", $start_date);

        }
    }

    protected function responseJson($message, $code = 200, $data = null)
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
