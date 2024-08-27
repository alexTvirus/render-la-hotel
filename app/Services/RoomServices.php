<?php


namespace App\Services;


use App\Models\Rooms as RoomModel;
use Illuminate\Support\Facades\Storage;

class RoomServices extends BaseServices
{

    public function __construct(RoomModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
    }


    public function getRoomByBooking($bookings)
    {

        $query = $this->model->whereNotIn('id', function ($query) use ($bookings) {
            $query->select('room_id')->from('room_booking')->whereIn('booking_id', $bookings);
        })
            ->with(['roomTypePacket'=> function($query) {
                $query->select('room_type_packet.id', 'room_type_packet.room_type_id','room_type_packet.packet_id');
            }]);
        $rs = $query->get();
        return $rs;
    }

    public function getRoomByRoomTypePacket($roomTypePackets){
        return $this->model->whereIn("room_type_packet_id",$roomTypePackets)->get();
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
