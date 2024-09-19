<?php


namespace App\Services;


use App\Models\Packet as PacketModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PacketServices extends BaseServices
{

    public function __construct(PacketModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $limit = $request->get("limit", "");
        $query_array = $request->query();
        $tour = $query_array['tour'] ?? "";
		$checkAvailable = $query_array['checkAvailable'] ?? 0;
        $relations = $request->get("loadRelation", []);
        $query = PacketModel::query();
        //$query = $this->model;

        if (!empty($relations) && !empty($relations['packetImages'])) {
            $query->with("packetImages", function ($query) {
                $query->select("id",
                    "packet_id",
                    "url", "name",
                    "description");
            });
        }


        $query = $query->whereHas("roomTypePackets", function ($query) use ($tour) {
            if (!empty($tour)) {
                $start_date = Carbon::now()->format('Y-m-d');
                $query = $query
                    ->whereNotNull('room_type_packet.start_at')
                    ->whereNotNull('room_type_packet.end_at')
                    ->whereRaw("room_type_packet.start_at >= STR_TO_DATE(?, '%Y-%m-%d')", $start_date);
            } else {
                $query = $query
                    ->whereNull('room_type_packet.start_at')
                    ->whereNull('room_type_packet.end_at');
            }
        });

        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }

        $query->select("id", "base_price", "name_packet", "description");
        $data = $query->get();
        $this->preparePacket($data, $tour,$checkAvailable);

        return empty($limit) ? ($data) : ($data->paginate($limit));
    }

    public function preparePacket(&$data, $tour = 0 ,$checkAvailable =0)
    {

		
        if (!$tour) {
            $data->each(function ($packet) {
                unset($packet['roomTypePackets']);
            });
        }

        if (!$data->isEmpty() && $tour) {
            $newData = collect();
			$roomBookingServices = app()->make(RoomBookingServices::class);
            $data->each(function ($packet) use (&$newData,$roomBookingServices,$checkAvailable) {
                $roomPackets = $packet['roomTypePackets'] ?? [];
                unset($packet['roomTypePackets']);
				

				
                $roomPackets->each(function ($roomPacket) use (&$newData, $packet,$roomBookingServices,$checkAvailable) {
                    $newPacket = clone $packet;
                    $newPacket['room_type_packet_id'] = $roomPacket['id'];
                    $newPacket['room_type_id'] = $roomPacket['room_type_id'];
                    $roomPacket['start_at'] ? ($newPacket['tour_start_at'] = $roomPacket['start_at']
                    ) : ("");
                    $roomPacket['end_at'] ? ($newPacket['tour_end_at'] = $roomPacket['end_at']) : ("");
                    $roomPacket['number_guest'] ? ($newPacket['number_guest'] = $roomPacket['number_guest']) : ("");
                    $roomPacket['number_room'] ? ($newPacket['number_room'] = $roomPacket['number_room']) : ("");
					
					if($checkAvailable){
						$newPacket['isBooked'] = $roomBookingServices->checkTourIsBooked($roomPacket['id']);
					}
					
                    $newData->push($newPacket);
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
                    'room_type_packet.number_guest', 'room_type_packet.number_room');
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
