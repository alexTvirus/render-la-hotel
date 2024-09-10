<?php


namespace App\Services;


use App\Models\Packet;
use App\Models\Room as RoomModel;
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
            ->with(['roomTypePacket' => function ($query) {
                $query->select('room_type_packet.id', 'room_type_packet.room_type_id', 'room_type_packet.packet_id');
            }]);
        $rs = $query->get();
        return $rs;
    }

    public function getRoomByRoomTypePacketsAndBooking($roomTypePackets, $bookings)
    {
        $query = $this->model
            ->whereIn("room_type_packet_id", $roomTypePackets)
            ->whereNotIn('id', function ($query) use ($bookings) {
                $query->select('room_id')->from('room_booking')->whereIn('booking_id', $bookings);
            });
        $rs = $query->get();
        return $rs;
    }

    public function getRoomByIdsAndPacket(&$rooms){
        $room_type_packets = $rooms->pluck("room_type_packet_id");

        $query = Packet:: select(
            "packets.id",
            "room_type_packet.id as room_type_packet_id",
			"room_types.name as room_type_name",
            "packets.name_packet"
            );
        $query
            ->join('room_type_packet', 'room_type_packet.packet_id', 'packets.id')
			->join('room_types', 'room_type_packet.room_type_id', 'room_types.id')
            ->whereIn('room_type_packet.id', $room_type_packets)
        ;
        $room_type_packets = $query->get();

        $rooms->each(function ($room)use($room_type_packets){
            $room_type_packets->each(function ($room_type_packet)use($room){
                if($room->room_type_packet_id == $room_type_packet->room_type_packet_id){
                    $room->packet = $room_type_packet->name_packet;
					$room->room_type = $room_type_packet->room_type_name;
                }
            });
        });

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
