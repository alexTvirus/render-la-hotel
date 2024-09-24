<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\AmenityServices;
use App\Tranformers\AmenityResource\AmenityDetailResource;
use App\Tranformers\AmenityResource\AmenityListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AmenityController extends BaseController
{

    private $amenityServices;
    public function __construct(AmenityServices $amenityServices)
    {
        $this->amenityServices = $amenityServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->amenityServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new AmenityListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new AmenityListResource($lists));
    }
}
