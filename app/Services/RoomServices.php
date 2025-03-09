<?php


namespace App\Services;


use App\Models\Packet;
use App\Models\Room as RoomModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class RoomServices extends BaseServices
{


    public function __construct(RoomModel $model)
    {
        parent::__construct($model);

    }

    public function index($request)
    {
        $limit = $request->get("limit");
        $query_array = $request->query();
        $checkin = $query_array['checkin_at'] ?? "";
        $checkout = $query_array['checkout_at'] ?? "";
        $room_type_id = $query_array['room_type_id'] ?? "";
		$selectTour = $query_array['selectTour'] ?? "";
        $packet_id = $query_array['packet_id'] ?? "";
		$relations = $request->get("loadRelation", []);


        $query = $this->model;

        if (!empty($checkin) && !empty($checkout) && !empty($room_type_id) && !empty($packet_id) && !empty($selectTour)) {
			// room de set cho tour :
			// 1. tat ca booking cua room do phai nho hon ngay bat dau tour (vd: booking 1/2 - 3/2 , 1/3- 4/3 , thi tour phai start 5/3 moi dc lay phong nay)
            // ko lay room da dc dat trong ngay in-out
            // ko lay room co ngay tour trong ngay in-out
            $bookingServices = app()->make(BookingServices::class);
            $bookings = $bookingServices->getBookingByNotAvailble($request)->pluck("id");
            $query = $query->whereNotIn('id', function ($query) use ($bookings) {
                $query->select('room_id')->from('room_booking')->whereIn('booking_id', $bookings);
            });
//            $query = $query->WhereDoesntHave('roomTypePacket', function ($query) use ($checkin, $checkout){
//                $query
//                    ->select("room_type_packet.id")
//                    ->where(function ($query) use ($checkin, $checkout) {
//                    $query->orwhere(function ($query) use ($checkin, $checkout) {
//                        $query
//                            ->whereRaw("room_type_packet.start_at <= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $checkin)
//                            ->whereRaw("room_type_packet.end_at >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $checkin);
//                    });
//                    $query->orwhere(function ($query) use ($checkin, $checkout) {
//                        $query
//                            ->whereRaw("room_type_packet.start_at >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $checkin)
//                            ->whereRaw("room_type_packet.start_at <= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $checkout);
//                    });
//                });
//            });

            $query = 
			
			$query->WhereDoesntHave('roomTypePacket', function ($query) use ($checkin, $room_type_id,$packet_id){
                // nhung query true se ko dc lay
                // tat ca false moi dc lay
                // 1 true se ko dc lay
                $query
                    ->select("room_type_packet.id")
                    ->where(function ($query) use ($checkin) {
                        $now_date = Carbon::now()->format('Y-m-d');
                        $query
                            ->whereNotNull("room_type_packet.end_at")
                            ->where(function ($query)use ($checkin,$now_date) {
                                $query->orWhereRaw("room_type_packet.end_at > STR_TO_DATE(?, '%Y-%m-%d')", $now_date)
                                    ->orWhereRaw("room_type_packet.end_at > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $checkin);
                            });

                    })
                    ->orWhere("room_type_id",'!=',$room_type_id)
                    ->orWhere("packet_id",'!=',$packet_id)
                    ->orWhere("isTour",0);
            });
        }

		if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }

		$query = $query
		->orderBy('rooms.id', 'desc')
		->orderBy('rooms.updated_at', 'desc');
        $rs = empty($limit) ? ($query->get()) : ($query->paginate($limit));
//        $this->model->where("ewf", 12)->get();
        return $rs;
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
            ->whereIn("room_type_packet_id", $roomTypePackets ?? [])
            ->whereNotIn('id', function ($query) use ($bookings) {
                $query->select('room_id')->from('room_booking')->whereIn('booking_id', $bookings ?? []);
            });
        $rs = $query->get();
        return $rs;
    }

    public function getRoomByIdsAndPacket(&$booking)
    {

        $room_type_packets = $booking->rooms->pluck("room_type_packet_id");

        $query = Packet:: select(
            "packets.id",
            "room_type_packet.id as room_type_packet_id",
            "room_type_packet.start_at as room_type_packet_start_at",
            "room_type_packet.end_at as room_type_packet_end_at",
            "room_type_packet.number_room as room_type_packet_number_room",
            "room_types.name as room_type_name",
            "packets.name_packet"
        );
        $query
            ->join('room_type_packet', 'room_type_packet.packet_id', 'packets.id')
            ->join('room_types', 'room_type_packet.room_type_id', 'room_types.id')
            ->whereIn('room_type_packet.id', $room_type_packets);
        $room_type_packets = $query->get();

        $isTour = false;
        $tour = [];

        $booking->rooms->each(function ($room) use ($room_type_packets, &$tour, &$isTour) {
            $room_type_packets->each(function ($room_type_packet) use ($room, &$tour, &$isTour) {
                if ($room->room_type_packet_id == $room_type_packet->room_type_packet_id) {
                    $room->packet = $room_type_packet->name_packet;
                    $room->room_type = $room_type_packet->room_type_name;
                    if (!$isTour) {
                        if ($room_type_packet->room_type_packet_start_at && $room_type_packet->room_type_packet_end_at) {
                            $isTour = true;
                            $tour["tour_start_at"] = $room_type_packet->room_type_packet_start_at;
                            $tour["tour_end_at"] = $room_type_packet->room_type_packet_end_at;
                            $tour["tour_number_room"] = $room_type_packet->room_type_packet_number_room;
                        }
                    }

                }
            });
        });
        $booking['tour'] = $tour;

    }

    public function show($request,$id)
    {
		$query_array = $request->query();
        $relations = $request->get("loadRelation", []);

		$query = $this->model;
		if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }

        $query = $query->where('id', $id);
		$data = $query->first();
        return $data;
    }

    public function insertOrUpdate(array $attributes)
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

    public function save(array $attributes)
    {


        if (!empty($attributes['room_type_id']) &&
            !empty($attributes['packet_id'])) {

            $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
            $entity = $roomTypePacketServices->getRoomTypePacketByRoomTypeAndPacket($attributes['room_type_id'],
                $attributes['packet_id']);
            if (!$entity->isEmpty()) {
				$idTemp =  $attributes['id']??null;
				//unset($attributes['id']);
                $attributes['room_type_packet_id'] = $entity->first()->id;
				$attributes['id'] = $attributes['room_type_packet_id'];
				$entity = $roomTypePacketServices->save($attributes);
				$attributes['id'] = $idTemp;
            } else {
				unset($attributes['id']);
				//$data['room_type_id'] = $attributes['room_type_id'];
                $entity = $roomTypePacketServices->save($attributes);
                $attributes['room_type_packet_id'] = $entity->id;
            }
			
			

            return $this->insertOrUpdate($attributes);
        } else {
            return $this->insertOrUpdate($attributes);
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
