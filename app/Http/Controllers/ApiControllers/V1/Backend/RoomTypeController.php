<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

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
        $entity = $this->roomTypeServices->show($code,$request);
        return (new RoomTypeDetailResource($entity));
    }

    public function index(Request $request)
    {
        $lists = $this->roomTypeServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new RoomTypeListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new RoomTypeListResource($lists));
    }
}
