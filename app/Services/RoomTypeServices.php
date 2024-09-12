<?php


namespace App\Services;


use App\Models\RoomType as RoomTypeModel;
use Illuminate\Support\Facades\Storage;
use stdClass;
use function PHPUnit\Framework\MockObject\object;

class RoomTypeServices extends BaseServices
{
    private $roomServices;
    private $packetServices;
    private $roomTypePacketServices;
    private $bookingServices;
    private $roomBookingServices;

    public function __construct(RoomTypeModel $model,
                                BookingServices $bookingServices,
                                RoomServices $roomServices,
                                PacketServices $packetServices,
                                RoomTypePacketServices $roomTypePacketServices
        , RoomBookingServices $roomBookingServices)
    {
        parent::__construct($model);
        $this->bookingServices = $bookingServices;
        $this->roomServices = $roomServices;
        $this->packetServices = $packetServices;
        $this->roomTypePacketServices = $roomTypePacketServices;
        $this->roomBookingServices = $roomBookingServices;
    }

    public function index($request)
    {
        $limit = $request->get('limit', RoomTypeModel::LIMIT_PAGE);
        $query_array = $request->query();


        $query = $this->model;


        //filter theo rating?

        //filter theo gói packet
        $packets = $query_array['packets'] ?? "{}";
		$ratings = $query_array['ratings'] ?? "{}";
		$packets = json_decode($packets, TRUE);
		$ratings = json_decode($ratings, TRUE);
		
        if (!empty($packets)  || !empty($ratings)) {
            
            $query =$query->whereHas("packets", function ($query) use ($packets,$ratings) {
                $query = $query
                    ->select("packets.id"
                );
				if(!empty($packets)){
					$query = $query->whereIn('packets.id', $packets);
					
				}
				if(!empty($ratings)){
					$query = $query->whereIn('room_type_packet.rate', $ratings);
				}
                    
            });
        }

        $price = $query_array['price'] ?? [];
        if (!empty($price)) {
            $price = json_decode($price, TRUE);
            $query = $query->where('base_price', ">=", $price['min'])
                ->where('base_price', "<=", $price['max']);
        }


		$sortBy = $query_array['sortBy'] ?? 1;
		if (!empty($sortBy)) {
			$sortBy==1?$query->orderBy('base_price', 'asc'):$query->orderBy('base_price', 'desc');
		}
		
        //lay tat ca room type
        $roomtypes = $query->get();

        // điều kiện này để cuối cùng, vì sau khi thực thi các đk trên tìm ra roomtype thì
        // sẽ lấy roomtype tìm đc thực thi tiếp
        if (!empty($request['checkin_at']) && !empty($request['checkout_at'])) {
            // get room voi dieu kien cua booking
            $bookings = $this->bookingServices->getBookingByNotAvailble($request)->pluck("id");
            $rooms = $this->roomServices->getRoomByBooking($bookings);
            // tim packet tuong ung voi room
            // todo: chi lay ra packet con phong neu co dieu kien booking
            $roomtypes->each(function ($item, $key) use ($rooms, $roomtypes) {
                $packetIds = collect();
                $rooms->each(function ($room) use ($item, &$packetIds) {
                    if ($room->roomTypePacket->room_type_id == $item->id)
                        $packetIds->push($room->roomTypePacket->packet_id);
                });
                if ($packetIds->isEmpty()) {
                    // room ko co packet ko hien thi
                    $roomtypes->forget($key);
                } else {
                    $this->prepareRoomType($item, $packetIds);
                }
            });
            return $roomtypes->paginate($limit);
        } else {
            $roomtypes->each(function ($item, $key) use ($roomtypes) {
                // kiem tra xem co phong ung voi id do ko
                // neu co thi moi hien thi
                if (!$item->rooms->isEmpty()) {
                    $packetIds = $item->roomTypePackets->pluck("packet_id");
                    $this->prepareRoomType($item, $packetIds);
                } else {
                    // ko co phong thi ko hien thi
                    $roomtypes->forget($key);
                }

            });
            return $roomtypes->paginate($limit);
        }
    }

    public function prepareRoomType(&$item, $packetIds)
    {
        $this->preparePacket($item, $packetIds);
        $this->prepareAmenities($item);
        $this->prepareRoomTypeImage($item);
        unset($item['rooms']);
        unset($item['roomTypePackets']);
    }

    public function prepareRoomTypeImage(&$item)
    {
        $item->roomTypeImages->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at', 'room_type_id',
            'image_type_id']);
    }

    public function preparePacket(&$roomType, $packetIds)
    {
        if (count($packetIds) > 0) {
            $packets = $this->packetServices->getPacketByIdsWithBenefits($packetIds)
                ->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
            $this->prepareRatings($roomType, $packets);
            $roomType['packets'] = $packets;
            $roomType->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
        } else {
            $roomType['packets'] = [];
        }

    }

    public function prepareAmenities(&$roomType)
    {
        $roomType->amenities->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
    }

    public function prepareRatings(&$roomType, &$packets)
    {
        $roomType->roomTypePackets->each(function ($item, $key) use (&$packets) {
            $packet = $packets->firstWhere("id", $item->packet_id);
            $packet ?
                ($packet['rating'] = $item->rate ?? 0) :
                ($packet['rating'] = 0);

        });
		
		$max = $packets->max("rating");
		$roomType['rating'] = $max;
        unset($roomType['ratings']);
    }

    public function prepareRatings1(&$roomType, &$packets)
    {
        $ratings = $roomType->ratings->groupBy('room_type_packet_id');
        $ratings = $ratings->map(function ($items) {
            return $items->sum('rate');
        });
        $roomType->roomTypePackets->each(function ($item, $key) use ($ratings, &$packets) {
            $packet = $packets->firstWhere("id", $item->packet_id);
            $packet ?
                ($packet['rating'] = $ratings->get($item->id) ?? 0) :
                ($packet['rating'] = 0);

        });
        unset($roomType['ratings']);
    }


    public function getRoomTypeById($id)
    {
        $data = $this->model->where('id', $id)->first();
        if (!$data) {
            return collect();
        }
        return $data;
    }

    public function show($id, $request = [])
    {
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            if (!$data->rooms->isEmpty()) {
                if (isset($request['checkin_at']) || isset($request['checkout_at'])) {
//                    $roomBookings = $this->roomBookingServices->getNotAvailableByBooking($request)->pluck("room_id")->toArray();
//                    if (!in_array($data->id, $roomBookings)) {
//
//                    }
                    // get room voi dieu kien cua booking
                    $bookings = $this->bookingServices->getBookingByNotAvailble($request)->pluck("id");
                    $rooms = $this->roomServices->getRoomByBooking($bookings);
                    // tim packet tuong ung voi room
                    // todo: chi lay ra packet con phong neu co dieu kien booking

                    $packetIds = collect();
                    $rooms->each(function ($room) use ($data, &$packetIds) {
                        if ($room->roomTypePacket->room_type_id == $data->id)
                            $packetIds->push($room->roomTypePacket->packet_id);
                    });
                    $this->prepareRoomType($data, $packetIds);
                } else {
                    $packetIds = $data->roomTypePackets->pluck("packet_id");
                    $this->prepareRoomType($data, $packetIds);
                }
            } else {
                $data = collect();
            }
        }
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
