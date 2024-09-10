<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

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
        $lists = $this->packetServices->index($request);
        return (new PacketListResource($lists));
    }
}
