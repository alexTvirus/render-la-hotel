<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\RatingServices;
use App\Services\UserServices;
use App\Tranformers\RatingResource\RatingDetailResource;
use App\Tranformers\RatingResource\RatingListResource;
use App\Tranformers\RatingResource\RatingRoomListResource;
use App\Tranformers\UserResource\UserListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{

    private $userServices;
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->userServices->index($request);

        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new UserListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new UserListResource($lists));

    }

}
