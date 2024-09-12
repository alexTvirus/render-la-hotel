<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

use App\Http\Controllers\BaseController;

use App\Services\RatingServices;
use App\Tranformers\RatingResource\RatingDetailResource;
use App\Tranformers\RatingResource\RatingListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RatingController extends BaseController
{

    private $ratingServices;
    public function __construct(RatingServices $ratingServices)
    {
        $this->ratingServices = $ratingServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->ratingServices->index($request);
        return (new RatingListResource($lists));
    }

    public function store(Request $request)
    {
        $info = $request->only([
            'id',
            "comment",
            "rate",
            "packet_id",
            "room_type_id",
        ]);
        DB::beginTransaction();
        try {
            $entity = $this->ratingServices->save($info);
            DB::commit();
            return $this->responseJson('success', Response::HTTP_OK, new RatingDetailResource($entity));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
