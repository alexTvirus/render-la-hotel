<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\DashBoardServices;
use App\Tranformers\DashBoardResource\DashBoardDetailResource;
use App\Tranformers\DashBoardResource\DashBoardListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashBoardController extends BaseController
{
    private $dashBoardServices;


    public function __construct(DashBoardServices $dashBoardServices)
    {
        $this->dashBoardServices = $dashBoardServices;

        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->dashBoardServices->index($request);
        return (new DashBoardListResource($lists));
    }
}
