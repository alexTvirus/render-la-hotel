<?php


namespace App\Services;


use App\Models\Rating as RatingModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class RatingServices extends BaseServices
{
    private $roomTypePacketServices;

    public function __construct(RatingModel $model, RoomTypePacketServices $roomTypePacketServices)
    {
        parent::__construct($model);
        $this->roomTypePacketServices = $roomTypePacketServices;
    }

    public function index($request)
    {
        $limit = $request->get("limit", "");
        $query = $this->model;
        $query_array = $request->query();
        $rate = $query_array['rate'] ?? "";
        $roomTypeid = $query_array['room_type_id'] ?? "";
        $relations = $request->get("loadRelation", []);

        $room_type_id = $request->get("room_type_id");
        $packet_id = $request->get("packet_id");
        if (!empty($room_type_id) && !empty($packet_id)) {
            $roomTypePacket = $this->roomTypePacketServices
                ->getRoomTypePacketByPacketIdAndRoomTypeId($request["room_type_id"], $request["packet_id"]);
            if (empty($roomTypePacket)) {
                return collect();
            }
            $query = $query->where("room_type_packet_id", $roomTypePacket->id);
            $query = $query->with('customer');
        }
        if (!empty($rate)) {
            $query = $query->where("rate", $rate);
        }
        if (!empty($roomTypeid)) {
            $query = $query->with(['roomTypePacket' => function ($query) use ($roomTypeid) {
                $query = $query->where("room_type_packet.room_type_id", $roomTypeid);
            }]);
        }

        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }

        $this->timeCondition($request, $query, "ratings");
		
		$query->orderBy('updated_at','desc');

        $data = empty($limit) ? ($query->get()) : ($query->paginate($limit));
        //$query->get();
        //$this->model->where("dsd",12)->get();


        return $data;

    }


    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }

    // todo: khi nguời dùng thay đổi rating , thì phải cập nhật bảng room type packet
    public function save(array $attributes)
    {
        if (empty($attributes['room_type_packet_id'])) {
            $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
            $roomTypePacket = $roomTypePacketServices
                ->getRoomTypePacketByPacketIdAndRoomTypeId($attributes["room_type_id"], $attributes["packet_id"]);
            if (empty($roomTypePacket)) {
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
