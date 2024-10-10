<?php


namespace App\Services;


use App\Models\Packet as PacketModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PacketServices extends BaseServices
{
    private $request;

    public function __construct(PacketModel $model)
    {
        parent::__construct($model);
    }

    public function uploadGGDrive($request, &$packet)
    {
        $file = [];
        $file['link_packet_img']['file'] = $request->file('link_packet_img');
        if (!empty($file['link_packet_img']['file'])) {
            $driveService = Storage::disk('google');

            $newPermission = app()->make('googlePermission');

            $folderId = app()->make('googleFolderId');

            $fileToUpload = $this->postGGDrive($driveService, $file['link_packet_img']['file'], $folderId);
            if ($fileToUpload) {
                $driveService->permissions->create($fileToUpload->id, $newPermission);
                $file['link_packet_img']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000-rw';
            }


            if (!empty($file['link_packet_img']['url'])) {
                $packet['url'] = $file['link_packet_img']['url'];
                $packet['url_backup'] = $file['link_packet_img']['url'];
            }

            return $file;
        }
        return null;
    }

    public function index($request)
    {
        $this->request = $request;
        $limit = $request->get("limit", "");
        $query_array = $request->query();
        $tour = $query_array['tour'] ?? "";
        $selectTour = $query_array['selectTour'] ?? "";
        $manageTour = $query_array['manageTour'] ?? "";
        $all = $query_array['all'] ?? "";
        $checkAvailable = $query_array['checkAvailable'] ?? 0;
        $relations = $request->get("loadRelation", []);
        $query = PacketModel::query();
        //$query = $this->model;

        if (empty($all)) {
            if (!empty($manageTour)) {
                $query = $query->whereHas("roomTypePackets", function ($query) {

                    $query = $query
                        ->whereNotNull('room_type_packet.start_at')
                        ->whereNotNull('room_type_packet.end_at')
                        ->where('room_type_packet.isTour', 1);

                });

//                $query->join('room_type_packet',function ($join){
//                    $join->on(function ($query) {
//                        $query->on('room_type_packet.packet_id', '=', 'packets.id');
//                    })
//                        ->whereNotNull('room_type_packet.start_at')
//                        ->whereNotNull('room_type_packet.end_at')
//                        ->where('room_type_packet.isTour', 1)
//                    ;
//                });

            } else if (!empty($tour)) {
                $query = $query->whereHas("roomTypePackets", function ($query) {
                    $start_date = Carbon::now()->format('Y-m-d');
                    $query = $query
                        ->whereNotNull('room_type_packet.start_at')
                        ->whereNotNull('room_type_packet.end_at')
                        ->where('room_type_packet.isTour', 1)
                        ->whereRaw("room_type_packet.start_at >= STR_TO_DATE(?, '%Y-%m-%d')", $start_date);

                });

//                $query->join('room_type_packet',function ($join){
//                    $join->on(function ($query) {
//                        $query->on('room_type_packet.packet_id', '=', 'packets.id');
//                    })
//                        ->whereNotNull('room_type_packet.start_at')
//                        ->whereNotNull('room_type_packet.end_at')
//                        ->where('room_type_packet.isTour', 1)
//                    ;
//                });

            } else if (!empty($selectTour)) {
                $query = $query->whereHas("roomTypePackets", function ($query) {
                    $query = $query
                        ->whereNull('room_type_packet.start_at')
                        ->whereNull('room_type_packet.end_at')
                        ->where('room_type_packet.isTour', 1);
                });
            } else {
                $query = $query->whereHas("roomTypePackets", function ($query) {
                    $query = $query
                        ->where('room_type_packet.isTour', 0);
                });
            }
        }


//		else{
//			$query= $query->where(function($query){
//				 $query->whereDoesntHave("roomTypePackets")
//                    ->orWhere(function ($query){
//						 $query->whereHas("roomTypePackets", function ($query) {
//								$query = $query
//										->whereNull('room_type_packet.start_at')
//										->whereNull('room_type_packet.end_at');
//							});
//					});
//			});
//			// 2 dk or
//			// 1. neu ko co thi lay
//			//	2. neu co whereHas thi xet khac null
//
//		}


        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }

        $query->select("packets.id", "packets.base_price", "packets.name_packet", "packets.description");
        $query = $query->orderBy('packets.updated_at', 'desc');
        $data = $query->get();
        //$this->model->where("e", 1)->get();
        $this->preparePacket($data, $tour, $checkAvailable);

        return empty($limit) ? ($data) : ($data->paginate($limit));
    }

    public function preparePacket(&$data, $tour = 0, $checkAvailable = 0)
    {
        if (!$tour) {
            $data->each(function ($packet) {
                unset($packet['roomTypePackets']);
            });
            return;
        }

        if (!$data->isEmpty() && $tour) {

            $newData = collect();
            $roomBookingServices = app()->make(RoomBookingServices::class);
            $data->each(function ($packet) use (&$newData, $roomBookingServices, $checkAvailable) {
                $roomPackets = $packet['roomTypePackets'] ?? [];
                unset($packet['roomTypePackets']);


                $roomPackets->each(function ($roomPacket) use (&$newData, $packet, $roomBookingServices, $checkAvailable) {
                    $newPacket = clone $packet;
                    if (!empty($roomPacket['start_at']) && !empty($roomPacket['end_at'])) {
                        $newPacket['room_type_packet_id'] = $roomPacket['id'];
                        $newPacket['room_type_id'] = $roomPacket['room_type_id'];
                        $roomPacket['start_at'] ? ($newPacket['tour_start_at'] = $roomPacket['start_at']
                        ) : ("");
                        $roomPacket['end_at'] ? ($newPacket['tour_end_at'] = $roomPacket['end_at']) : ("");
                        $roomPacket['number_guest'] ? ($newPacket['number_guest'] = $roomPacket['number_guest']) : ("");
                        $roomPacket['number_room'] ? ($newPacket['number_room'] = $roomPacket['number_room']) : ("");

                        if ($checkAvailable) {
                            $newPacket['isBooked'] = $roomBookingServices->checkTourIsBooked($roomPacket['id']);
                        }

                        $newData->push($newPacket);
                    }

                });


            });
            $data = $newData;
        }

    }

    public function getAll($request)
    {

        $query = PacketModel::query();
        //$query = $this->model;

        $query->with("packetImages", function ($query) {
            $query->select("id",
                "packet_id",
                "url", "name",
                "description");
        });


        $query->select("id", "base_price", "name_packet", "description");
        return $query->get();
    }

    public function prepareRoomTypeImage(&$item)
    {
        $item->roomTypeImages->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at', 'room_type_id',
            'image_type_id']);
    }

    public function getPacketByIdsWithBenefits($ids)
    {
        $query = $this->model->whereIn('id', $ids)
            ->with(['roomTypePackets' => function ($query) {
                $query->select('room_type_packet.packet_id', 'room_type_packet.room_type_id',
                    'room_type_packet.rate', 'room_type_packet.start_at', 'room_type_packet.end_at',
                    'room_type_packet.number_guest', 'room_type_packet.number_room', 'room_type_packet.isTour');
            }])
            ->with(['benefits' => function ($query) {
                $query->select('benefits.id', 'benefits.name', 'benefits.price', 'benefits.description');
            }]);

        return $query->get();
    }

    public function getPacketByIds($ids)
    {
        $query = $this->model->whereIn('id', $ids);
        return $query->get();
    }


    public function show($request, $id)
    {
        $query_array = $request->query();
        $relations = $request->get("loadRelation", []);
        $room_type_id = $request->get("room_type_id", []);


        $query = $this->model;
        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }
        if (!empty($room_type_id)) {
            $query = $query->with("roomTypePackets", function ($query) use ($room_type_id) {
                $query = $query
                    ->where('room_type_packet.room_type_id', $room_type_id);
            });

            $query = $query->with("roomTypes", function ($query) use ($room_type_id) {
                $query = $query
                    ->where('room_types.id', $room_type_id);
            });

            $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
            $roomTypePackets = $roomTypePacketServices->getRoomTypePacketByRoomTypeAndPacket($room_type_id,
                $id);
            if (!$roomTypePackets->isEmpty()) {
                $room_type_packet_id = $roomTypePackets->first()->id;
                $query = $query->with("rooms", function ($query) use ($room_type_packet_id) {
                    $query = $query
                        ->where('rooms.room_type_packet_id', $room_type_packet_id);
                });
            }
        } else {
            $query = $query->with("roomTypePackets");
        }


        $query = $query->where('id', $id);
        $data = $query->first();
        return $data;
    }

    public function checkOldImage($entity,$attributes){
        $packetImageServices = app()->make(PacketImageServices::class);
        // ktr xoa img cu
        $imageList = $attributes['imageIds'];

        $oldImages = $entity->packetImages->pluck('id')->all();

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
            $packetImageServices->delete($id);
        }
    }

    public function insertOrUpdate($request, array $attributes)
    {
        $packetImageServices = app()->make(PacketImageServices::class);
        //
        $entity = null;
        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();

                $attributes['packet_id'] = $entity->id;
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
								 $info['packet_id']=$attributes['packet_id'];
                                $packetImageServices->save($info);
                            }
                        };
                    }


                }

            }
        } else {

            $entity = $this->model->create($attributes);
            $attributes['packet_id'] = $entity->id;
            if (!empty($attributes['link_img'])) {
                $fileList = $attributes['link_img']['fileList'];
                if (!empty($fileList) && count($fileList) > 0) {
                    foreach ($fileList as $item) {
                        $attributes['url'] = $item['response']['data'] ?? "";
                        if (!empty($attributes['url'])) {
                            $info['image_type_id'] = "1";
                            $info['url']=$attributes['url'];
							 $info['packet_id']=$attributes['packet_id'];
                            $packetImageServices->save($info);
                        }
                    };
                }
            }

        }



        return $entity;
    }

    public function saveTour($request, array $attributes)
    {
        $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
        $roomServices = app()->make(RoomServices::class);

        // luu / sua gia tri cua packet
        $entity = $this->insertOrUpdate($request, $attributes);
        $roomTypePacket = null;
        if (!empty($entity)) {
            // update gia tri roomptypepacket
            $info['packet_id'] = $entity->id;
			$info['room_type_id'] = $attributes['room_type_id'];
			$info['number_room'] = $attributes['number_room'];
            $info['start_at'] = $attributes['start_at'];
            $info['end_at'] = $attributes['end_at'];
            $roomTypePackets = $roomTypePacketServices->getRoomTypePacketByRoomTypeAndPacket($info['room_type_id'],
                $info['packet_id']);
            $info['isTour'] = 1;
            if (!$roomTypePackets->isEmpty()) {
                $info['id'] = $roomTypePackets->first()->id;
                $roomTypePacket = $roomTypePacketServices->save($info);
            } else {
                $roomTypePacket = $roomTypePacketServices->save($info);
            }



        }

        // update gia tri cua room

        // neu goi tour se truyen room_type_packet_id truc tiep
        // neu tour room_number la mang
        // if (!empty($attributes['room_ids'])
        //     && is_array($attributes['room_ids'])) {
        //     $isSuccess = true;
        //     if (!empty($roomTypePacket)) {
        //         $roomTemp = $attributes['room_ids'];
        //         $oldRooms = $roomTypePacket->rooms->pluck('id')->all();

        //         $listtemp = [];
        //         if (isset($attributes['room_ids'])) {
        //             foreach ($attributes['room_ids'] as $index => $room) {
        //                 if (in_array($room, $oldRooms)) {
        //                     $listtemp[] = $room;
        //                 }
        //             }
        //         }

        //         // lọc các id cần xóa
        //         $listdelete = array_diff($oldRooms, $listtemp);

        //         foreach ($roomTemp as $roomId) {
        //             $info['id'] = $roomId;
        //             $info['room_type_packet_id'] = $roomTypePacket->id;
        //             $isSuccess = $roomServices->insertOrUpdate($info) ? ($isSuccess && true) : (false);
        //         }

        //         foreach ($listdelete as $id) {
        //             $attributes['id'] = $id;
        //             $attributes['room_type_packet_id'] = null;
        //             $isSuccess = $roomServices->insertOrUpdate($attributes) ? ($isSuccess && true) : (false);
        //         }


        //         if (!$isSuccess)
        //             return null;
        //     } else {
        //         return null;
        //     }
        // }
        return $entity;

    }

    public function save($request, array $attributes)
    {
		$entity = $this->insertOrUpdate($request, $attributes);
        // update gia tri benefit-packet
        if(!empty($entity)){
            $benefits = $attributes['benefits']??[];
            $entity->benefits()->sync($benefits);
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
