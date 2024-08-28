<?php


namespace App\Services;


use App\Models\RoomTypePacket as RoomTypePacketModel;
use Illuminate\Support\Facades\Storage;

class RoomTypePacketServices extends BaseServices
{
    public function __construct(RoomTypePacketModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
    }


    public function getRoomTypePacketByRooms($ids)
    {
        $query = $this->model->whereIn('id', $ids);
        return $query->get();
    }

    public function getRoomTypePacketByRoomTypeAndPacket($roomType,$packet)
    {
        $query = $this->model
            ->where("room_type_id",$roomType)
            ->where('packet_id', $packet);
        return $query->get();
    }

    public function getRoomTypePacketByRoomTypeAndPackets($roomType,$packets)
    {
        $query = $this->model
            ->where("room_type_id",$roomType)
            ->whereIn('packet_id', $packets);
        return $query->get();
    }

    public function getRatings($ids)
    {
        $query = $this->model->whereIn('id', $ids)->ratings();
        return $query->get();
    }

    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }

    public function save(array $attributes)
    {
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
