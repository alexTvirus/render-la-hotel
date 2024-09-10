<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

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
        return (new AmenityListResource($lists));
    }
}
