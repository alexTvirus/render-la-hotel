<?php


namespace App\Services;


use App\Models\Rating as RatingModel;
use Illuminate\Support\Facades\Storage;

class RatingServices extends BaseServices
{
    private $roomTypePacketServices;
    public function __construct(RatingModel $model,RoomTypePacketServices $roomTypePacketServices)
    {
        parent::__construct($model);
        $this->roomTypePacketServices = $roomTypePacketServices;
    }

    public function index($request)
    {
        $limit = $request->get("limit",RatingModel::LIMIT_PAGE);
        $query = $this->model;

        $roomTypePacket =  $this->roomTypePacketServices
            ->getRoomTypePacketByPacketIdAndRoomTypeId($request["room_type_id"],$request["packet_id"]);
        if(empty($roomTypePacket)){
            return collect();
        }
        $query= $query->where("room_type_packet_id",$roomTypePacket->id);
        $query->with('customer');
        return $query->paginate($limit);
    }


    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }

    // todo: khi nguời dùng thay đổi rating , thì phải cập nhật bảng room type packet
    public function save(array $attributes)
    {
        if(empty($attributes['room_type_packet_id'])){
            $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
            $roomTypePacket = $roomTypePacketServices
                ->getRoomTypePacketByPacketIdAndRoomTypeId($attributes["room_type_id"],$attributes["packet_id"]);
            if(empty($roomTypePacket)){
                return null;
            }
            $attributes['room_type_packet_id'] = $roomTypePacket->id;
        }

        $attributes['customer_id'] = $this->getCurrentUser()->id;

        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();
                return $entity;
            } else {
                return null;
            }
        } else {
            $entity = $this->model->create($attributes);
            return $entity;
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
