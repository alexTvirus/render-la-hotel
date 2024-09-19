<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;


use App\Services\PacketServices;
use App\Tranformers\PacketResource\PacketDetailResource;
use App\Tranformers\PacketResource\PacketListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PacketController extends BaseController
{
    private $packetServices;
    public function __construct(PacketServices $packetServices)
    {
        $this->packetServices = $packetServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $request['loadRelation'] = [
            "roomTypePackets",
            "benefits",
            "packetImages"
        ];
		 $request['checkAvailable'] = 1;
        $lists = $this->packetServices->index($request);
		if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new PacketListResource($lists))->additional([
            'totalPage' => $lists->total(),
            'lastPage' => $lists->lastPage(),
            'currentPage' => $lists->currentPage(),
            'perPage' => (int)$lists->perPage(),
			]);

        }
        return (new PacketListResource($lists));
    }
}
