<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\RoomTypeServices;
use App\Tranformers\RoomTypeResource\RoomTypeDetailResource;
use App\Tranformers\RoomTypeResource\RoomTypeListResource;
use Illuminate\Http\Response;
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

    public function show(Request $request, $code)
    {
        $entity = $this->roomTypeServices->show($code, $request);
        if ($entity)
            return (new RoomTypeDetailResource($entity));
        else
            return $this->responseJson('fail',Response::HTTP_FAILED_DEPENDENCY,[]);
    }

    public function index(Request $request)
    {
        $lists = $this->roomTypeServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new RoomTypeListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new RoomTypeListResource($lists));
    }

    public function store(Request $request)
    {
        $info = $request->only([
            'base_price',
            'name',
            'description',
            'max_occupancy',
            'room_size',
            'bathrooms',
            'sleeps',
            'images',

        ]);
        DB::beginTransaction();
        try {
            $entity = $this->roomTypeServices->save($request,$info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new RoomTypeDetailResource($entity);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function update(Request $request, $id)
    {
        $info = $request->only([
            'base_price',
            'name',
            'description',
            'max_occupancy',
            'room_size',
            'bathrooms',
            'sleeps',
            'images',
        ]);
        $info['id'] = $id;
        DB::beginTransaction();
        try {
            $entity = $this->roomTypeServices->save($request,$info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new RoomTypeDetailResource($entity);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
	
	public function delete(Request $request, $id)
    {
        $entity = $this->roomTypeServices->delete($id);
        if ($entity)
            return $this->responseJson('success', Response::HTTP_OK, []);
        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);

    }
}
