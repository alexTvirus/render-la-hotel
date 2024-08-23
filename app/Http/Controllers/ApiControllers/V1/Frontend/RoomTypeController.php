<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

use App\Http\Controllers\BaseController;

use App\Services\RoomTypeServices;
use App\Tranformers\RoomTypeResource\RoomTypeDetailResource;
use App\Tranformers\RoomTypeResource\RoomTypeListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends BaseController
{
    private $roomTypeServices;


    public function __construct(RoomTypeServices $roomTypeServices)
    {
        $this->roomTypeServices = $roomTypeServices;

        parent::__construct();
    }

    public function show(Request $request,$code)
    {
        $entity = $this->roomTypeServices->show($code);
        return (new RoomTypeDetailResource($entity));
    }

    public function index(Request $request)
    {
        //$request['checkin_at'] = "2024-05-16";
        //$request['checkout_at'] ="2024-05-17";
        $lists = $this->roomTypeServices->index($request);
        return (new RoomTypeListResource($lists))->additional([
            'total' => $lists->total(),
            'lastPage' => $lists->lastPage(),
            'currentPage' => $lists->currentPage(),
            'perPage' => (int)$lists->perPage(),
        ]);
    }
}
