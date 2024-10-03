<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\RoomServices;
use App\Tranformers\RoomResource\RoomDetailResource;
use App\Tranformers\RoomResource\RoomListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
		$request['loadRelation'] = [
            "roomTypePacket:id,packet_id,room_type_id,isTour"
        ];
        $lists = $this->roomServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new RoomListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new RoomListResource($lists));
    }

    public function store(Request $request)
    {
        $info = $request->only([
            'room_number',
            'packet_id',
            'room_type_id',
            'isTour'

        ]);
        DB::beginTransaction();
        try {
            $entity = $this->roomServices->save($info);
            if(empty($entity)){
                DB::rollBack();
                return $this->responseJson("fail", Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return $this->responseJson("success", Response::HTTP_OK, $entity);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function update(Request $request, $roomId)
    {
        $info = $request->only([
            'id',
            'room_number',
            'packet_id',
            'room_type_id',
            'isTour'
        ]);
        $info['id'] = $roomId;
        DB::beginTransaction();
        try {
            $entity = $this->roomServices->save($info);
            DB::commit();
            return $this->responseJson("success", Response::HTTP_OK, $entity);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function show(Request $request, $roomId)
    {
		$request['loadRelation'] = [
            "roomTypePacket:id,packet_id,room_type_id,isTour"
        ];
        $entity = $this->roomServices->show($request,$roomId);
        if ($entity){
			return new RoomDetailResource($entity);
		}

        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
    }

    public function delete(Request $request, $roomId)
    {
        $entity = $this->roomServices->delete($roomId);
        if ($entity)
            return $this->responseJson('success', Response::HTTP_OK, []);
        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);

    }


}
