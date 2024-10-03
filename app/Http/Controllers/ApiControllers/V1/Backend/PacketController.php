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
             "benefits:benefits.id,benefits.price,benefits.name,benefits.description",
            "packetImages:packet_image.id,packet_image.packet_id,packet_image.url,packet_image.name,packet_image.description"
        ];
        $request['checkAvailable'] = 1;
        $lists = $this->packetServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new PacketListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new PacketListResource($lists));
    }


    public function show(Request $request, $packetId)
    {
		 $request['loadRelation'] = [
            "benefits:benefits.id,benefits.price,benefits.name,benefits.description",
            "packetImages:packet_image.id,packet_image.packet_id,packet_image.url,packet_image.name,packet_image.description"
        ];
        $entity = $this->packetServices->show($request,$packetId);
        if ($entity)
            return new PacketDetailResource($entity);
        else
            return $this->responseJson("fail", Response::HTTP_FAILED_DEPENDENCY, []);
    }

    public function store(Request $request)
    {
        $info = $request->only([
            'name_packet',
            'base_price',
            'description',
            'room_ids',
            'packet_id',
            'room_type_id',
            'start_at',
            'end_at',
            'number_guest',
            'number_room'
        ]);
        $query_array = $request->query();
        DB::beginTransaction();
        try {
            $tour = $query_array['tour'] ?? "";
            if (empty($tour))
                $entity = $this->packetServices->save($info);
            else
                $entity = $this->packetServices->saveTour($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson("fail", Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return $this->responseJson("success", Response::HTTP_OK, $entity);
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function update(Request $request, $packetId)
    {
        $info = $request->only([
            'id',
            'name_packet',
            'base_price',
            'description',
            'room_ids',
            'packet_id',
            'room_type_id',
            'start_at',
            'end_at',
            'number_guest',
            'number_room'
        ]);
        $info['id'] = $packetId;
        $query_array = $request->query();
        DB::beginTransaction();
        try {
            $tour = $query_array['tour'] ?? "";
            if (empty($tour))
                $entity = $this->packetServices->save($info);
            else
                $entity = $this->packetServices->saveTour($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson("fail", Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson("fail", Response::HTTP_BAD_GATEWAY, []);
        }
    }

	  public function delete(Request $request, $id)
    {
        $entity = $this->packetServices->delete($id);
        if ($entity)
            return $this->responseJson('success', Response::HTTP_OK, []);
        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);

    }
}
