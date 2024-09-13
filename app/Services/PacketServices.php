<?php


namespace App\Services;


use App\Models\Packet as PacketModel;
use Illuminate\Support\Facades\Storage;

class PacketServices extends BaseServices
{

    public function __construct(PacketModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {

        $query = PacketModel::query();


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
                    'room_type_packet.rate', 'room_type_packet.start_at','room_type_packet.end_at',
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
