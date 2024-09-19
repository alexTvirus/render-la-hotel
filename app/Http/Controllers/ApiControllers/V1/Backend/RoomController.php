<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\RoomServices;
use App\Tranformers\RoomResource\RoomDetailResource;
use App\Tranformers\RoomResource\RoomListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends BaseController
{
    private $roomServices;


    public function __construct(RoomServices $roomServices)
    {
        $this->roomServices = $roomServices;

        parent::__construct();
    }

    public function index(Request $request)
    {
		$lists = $this->roomServices->index($request);
		if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new RoomListResource($lists))->additional([
            'totalPage' => $lists->total(),
            'lastPage' => $lists->lastPage(),
            'currentPage' => $lists->currentPage(),
            'perPage' => (int)$lists->perPage(),
			]);

        }
        return (new RoomListResource($lists));
    }
}
