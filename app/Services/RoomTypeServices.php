<?php


namespace App\Services;


use App\Models\RoomType as RoomTypeModel;
use App\Models\RoomTypeImage;
use Carbon\Carbon;
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



        // filter theo tour


        //filter theo rating?

        //filter theo gói packet
        $tour = $query_array['tour'] ?? "";
		$all = $query_array['all'] ?? "";
        $packets = $query_array['packets'] ?? "{}";
        $ratings = $query_array['ratings'] ?? "{}";
		$selectTour = $query_array['selectTour'] ?? "";
		
		$selectNomal = $query_array['selectNomal'] ?? "";
        $packets = json_decode($packets, TRUE);
        $ratings = json_decode($ratings, TRUE);
		

		if(empty($all)){
			$query = $query->whereHas("packets", function ($query) use ($packets, $ratings, $tour,$selectTour,$selectNomal) {
				$query = $query
					->select("packets.id"
					);

					if (!empty($tour)) {
						$start_date = Carbon::now()->format('Y-m-d');
						$query = $query
							->whereNotNull('room_type_packet.start_at')
							->whereNotNull('room_type_packet.end_at')
							->where('room_type_packet.isTour',1)
							->whereRaw("room_type_packet.start_at >= STR_TO_DATE(?, '%Y-%m-%d')", $start_date);
					}
					else if(!empty($selectTour)){
						$query = $query
								->where('room_type_packet.isTour', 1);
					}
					else {
						$query = $query
								->where('room_type_packet.isTour', 0);
					}
					

				if (!empty($packets)) {
					$query = $query->whereIn('packets.id', $packets);

				}
				if (!empty($ratings)) {
					$query = $query
					->where('room_type_packet.isTour', 0)
					->whereIn('room_type_packet.rate', $ratings);
				}

			});
		}
		
		if(isset($query_array['roomTypes'])){
			$roomTypes = $query_array['roomTypes'] ?? "";
			$roomTypes = json_decode($roomTypes, TRUE);
			if(!empty($roomTypes)){
			 $query = $query->whereIn('id', $roomTypes);
			}else{
				return collect();
			}
		}
		
		

        $price = $query_array['price'] ?? [];
        if (!empty($price)) {
            $price = json_decode($price, TRUE);
            $query = $query->where('base_price', ">=", $price['min'])
                ->where('base_price', "<=", $price['max']);
        }


        $sortBy = $query_array['sortBy'] ?? '';
        if (!empty($sortBy)) {
            $sortBy == 1 ? $query->orderBy('room_types.base_price', 'asc') : $query->orderBy('room_types.base_price', 'desc');
        }else{
			$query = $query->orderBy('room_types.updated_at', 'desc');
		}

        //lay tat ca room type
        $roomtypes = $query->get();
		//$this->model->where("we",11)->get();

        // điều kiện này để cuối cùng, vì sau khi thực thi các đk trên tìm ra roomtype thì
        // sẽ lấy roomtype tìm đc thực thi tiếp
        if (!empty($request['checkin_at']) && !empty($request['checkout_at'])) {
            // get room voi dieu kien cua booking
            $bookings = $this->bookingServices->getBookingByNotAvailble($request)->pluck("id");
            $rooms = $this->roomServices->getRoomByBooking($bookings);

            // tim packet tuong ung voi room
            // todo: chi lay ra packet con phong neu co dieu kien booking
            $roomtypes->each(function ($item, $key) use ($rooms, $roomtypes, $tour) {
                $roomsAvailable = collect();
                $packetIds = collect();
                $rooms->each(function ($room) use ($item, &$packetIds, &$roomsAvailable) {
                    if ($room?->roomTypePacket?->room_type_id == $item->id) {
                        $packet_id = $room->roomTypePacket->packet_id;
                        $roomsAvailable->push(['packet_id' => $packet_id,
                            'room_id' => $room->id, 'room_number' => $room->room_number
                        ]);
                        $packetIds->push($packet_id);
                    }

                });
                $item['rooms_available'] = $roomsAvailable;
                if ($packetIds->isEmpty()) {
                    // room ko co packet ko hien thi
                    $roomtypes->forget($key);
                } else {
                    $this->prepareRoomType($item, $packetIds, $tour);
                }
            });

            return $roomtypes->paginate($limit);
        }  else {
            $roomtypes->each(function ($item, $key) use ($roomtypes, $tour) {
                // kiem tra xem co phong ung voi id do ko
                // neu co thi moi hien thi
                //if (!$item->rooms->isEmpty()) {
                    $packetIds = $item->roomTypePackets->pluck("packet_id");
                    $this->prepareRoomType($item, $packetIds, $tour);
                //} else {
                    // ko co phong thi ko hien thi
                //    $roomtypes->forget($key);
                //}

            });

            return $roomtypes->paginate($limit);
        }
    }


    public function prepareRoomType(&$item, $packetIds, $isTour = 0)
    {
        $this->preparePacket($item, $packetIds, $isTour);
        $this->prepareAmenities($item);
        $this->prepareRoomTypeImage($item);
        unset($item['rooms']);
        unset($item['roomTypePackets']);
        unset($item['rooms_available']);
    }

    public function prepareRoomTypeImage(&$item)
    {
        $item->roomTypeImages->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at', 'room_type_id',
            'image_type_id']);
    }

    public function preparePacket(&$roomType, $packetIds, $isTour)
    {
        if (count($packetIds) > 0) {
            $packets = $this->packetServices->getPacketByIdsWithBenefits($packetIds)
                ->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
            $this->prepareRatings($roomType, $packets);


            $newPackets = collect();
            $packets->each(function ($packet) use (&$newPackets, &$roomType, $isTour) {
                $roomPackets = $packet['roomTypePackets'] ?? [];
                unset($packet['roomTypePackets']);
                if ($roomPackets->contains(function ($roomPacket) use ($roomType, &$packet, $isTour) {

                    if ($isTour && $roomType->id == $roomPacket->room_type_id &&
                        $roomPacket['start_at'] && $roomPacket['end_at'] && $roomPacket['isTour']) {
                        $packet['tour_start_at'] = $roomPacket['start_at'];
                        $packet['tour_end_at'] = $roomPacket['end_at'];
                        $packet['number_guest'] = $roomPacket['number_guest'];
                        $packet['number_room'] = $roomPacket['number_room'];
                        return true;
                    }
                    if (!$isTour && $roomType->id == $roomPacket->room_type_id
                        &&
                        $roomPacket['isTour']==0) {
                        return true;
                    }
                    return false;


                })) {
                    $packet['rooms_available'] = collect();

                    if ($roomType['rooms_available'])
                        $roomType['rooms_available']->each(function ($room) use (&$packet) {
                            if ($room['packet_id'] === $packet->id) {
                                $packet['rooms_available']->push($room);
                            }
                        });

                    $newPackets->push($packet);
                }
            });
            $roomType['packets'] = $newPackets;


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
		$roomType->load(["roomTypePackets" => function($query){
			$query->where("isTour",0);
		}]);
		
		$avg = $roomType['roomTypePackets']?->avg("rate")??0;

		unset($roomType['roomTypePackets']);
		unset($roomType['room_type_packets']);
		
		
        $roomType->roomTypePackets->each(function ($item, $key) use (&$packets) {
            $packet = $packets->firstWhere("id", $item->packet_id);
            if ($packet) {
//                $ratings = $item->ratings()->with('customer')->get();
//                $packet['review']=$ratings;
                $packet['rating'] = $item->rate ?? 0;
            } else {
                $packet['rating'] = 0;
            }


        });
		
        $avg = number_format((float) $avg, 1, '.', '');
        $roomType['rating'] = $avg;
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
        $query_array = $request->query();
        $tour = $query_array['tour'] ?? "";
        $packet_id = $query_array['packet_id'] ?? "";
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            //if (!$data->rooms->isEmpty()) {
                if (isset($request['checkin_at']) || isset($request['checkout_at'])) {
                    // get room voi dieu kien cua booking
                    $bookings = $this->bookingServices->getBookingByNotAvailble($request)->pluck("id");
                    $rooms = $this->roomServices->getRoomByBooking($bookings);
                    // tim packet tuong ung voi room
                    // todo: chi lay ra packet con phong neu co dieu kien booking

                    $roomsAvailable = collect();
                    $packetIds = collect();
                    if (empty($packet_id)) {
                        $rooms->each(function ($room) use ($data, &$packetIds, &$roomsAvailable) {
                            if ($room?->roomTypePacket?->room_type_id == $data->id) {
                                $packet_id = $room->roomTypePacket->packet_id;
                                $roomsAvailable->push(['packet_id' => $packet_id,
                                    'room_id' => $room->id, 'room_number' => $room->room_number
                                ]);
                                $packetIds->push($packet_id);
                            }

                        });
                    } else {
                        $packetIds->push($packet_id);
                        $rooms->each(function ($room) use ($data, &$roomsAvailable, $packet_id) {
                            if ($room?->roomTypePacket?->room_type_id == $data?->id
                                && $room?->roomTypePacket?->packet_id == $packet_id) {
                                $roomsAvailable->push(['packet_id' => $room->roomTypePacket->packet_id,
                                    'room_id' => $room->id, 'room_number' => $room->room_number
                                ]);
                            }
                        });
                    }

                    $data['rooms_available'] = $roomsAvailable;
                    $this->prepareRoomType($data, $packetIds, $tour);
                } else {
                    $packetIds = $data?->roomTypePackets?->pluck("packet_id");
                    $this->prepareRoomType($data, $packetIds, $tour);
                }
            //} else {
            //    $data = collect();
            //}
        }
        return $data;
    }

    public function uploadImages($request,$entity){

    }

    public function checkOldImage($entity,$attributes){
        $roomTypeImageServices = app()->make(RoomTypeImageServices::class);
        // ktr xoa img cu
        $imageList = $attributes['imageIds'];

        $oldImages = $entity->roomTypeImages->pluck('id')->all();

        $listtemp = [];
        if (!empty($imageList)) {
            foreach ($imageList as $index => $img) {
                if (in_array($img, $oldImages)) {
                    $listtemp[] = $img;
                }
            }
        }

        // lọc các id cần xóa
        $listdelete = array_diff($oldImages, $listtemp);

        foreach ($listdelete as $id) {
            $roomTypeImageServices->delete($id);
        }
    }

    public function save($request,array $attributes)
    {
        $roomTypeImageServices = app()->make(RoomTypeImageServices::class);
        $entity = null;
        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if (!empty($entity)) {

                $entity->fill($attributes)->save();

                $attributes['room_type_id'] = $entity->id;
                if (!empty($attributes['link_img'])) {
                    $this->checkOldImage($entity,$attributes);
                    $fileList = $attributes['link_img']['fileList'];

                    if (!empty($fileList) && count($fileList) > 0) {
                        foreach ($fileList as $item) {
                            $attributes['url'] = $item['response']['data'] ?? "";
                            if (!empty($attributes['url'])) {
                                $info['id'] = $item['id'] ?? "";
                                $info['image_type_id'] = "1";
                                $info['url']=$attributes['url'];
								$info['room_type_id']=$attributes['room_type_id'];
                                $roomTypeImageServices->save($info);
                            }
                        };
                    }


                }

            }
        } else {
            $entity = $this->model->create($attributes);
            $attributes['room_type_id'] = $entity->id;
            if (!empty($attributes['link_img'])) {
                $fileList = $attributes['link_img']['fileList'];
                if (!empty($fileList) && count($fileList) > 0) {
                    foreach ($fileList as $item) {
                        $attributes['url'] = $item['response']['data'] ?? "";
                        if (!empty($attributes['url'])) {
                            $info['image_type_id'] = "1";
                            $info['url']=$attributes['url'];
							$info['room_type_id']=$attributes['room_type_id'];
                            $roomTypeImageServices->save($info);
                        }
                    };
                }
            }
        }
		
		if(!empty($entity)){
            $amenities = $attributes['amenities']??[];
            $entity->amenities()->sync($amenities);
        }

        return $entity;
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
