<?php


namespace App\Services;


use App\Models\Rating as RatingModel;
use Illuminate\Support\Facades\Storage;

class RatingServices extends BaseServices
{
    public function __construct(RatingModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
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
