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

    public function __construct(RoomTypeModel $model, RoomServices $roomServices,
                                PacketServices $packetServices,
                                RoomTypePacketServices $roomTypePacketServices)
    {
        parent::__construct($model);
        $this->roomServices = $roomServices;
        $this->packetServices = $packetServices;
        $this->roomTypePacketServices = $roomTypePacketServices;
    }

    public function index($request)
    {
        $limit = $request->get('limit', RoomTypeModel::LIMIT_PAGE);
        $query = $this->model;
        //lay tat ca room type
        $roomtypes = $query->get();

        //filter theo rating?

        //filter theo gói packet
        if (isset($request['packet'])) {
            $request['packet'];
            // co packet id >> tim room type packet
            // >> tim dc room type
            // >> set up du lieu cho room type
        }

        if (isset($request['price'])) {
            $request['price'];
            // co price >> tim so sanh base price cua room type
            // >> tim dc room type
            // >> set up du lieu cho room type
        }

        if (isset($request['rate'])) {
            $request['rate'];
            // co price >> tim so sanh base price cua room type
            // >> tim dc room type
            // >> set up du lieu cho room type
        }
        // điều kiện này để cuối cùng, vì sau khi thực thi các đk trên tìm ra roomtype thì
        // sẽ lấy roomtype tìm đc thực thi tiếp
        if (isset($request['checkin_at']) || isset($request['checkout_at'])) {
            // get room voi dieu kien cua booking
            $rooms = $this->roomServices->getRoomByBooking($request);
            // tim packet tuong ung voi room
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
                    $this->prepareRoomType($item,$packetIds);
                } else {
                    // ko co phong thi ko hien thi
                    $roomtypes->forget($key);
                }

            });
            return $roomtypes->paginate($limit);
        }
    }

    public function prepareRoomType(&$item,$packetIds)
    {
        $this->preparePacket($item, $packetIds);
        $this->prepareAmenities($item);
        $this->prepareRoomTypeImage($item);
        unset($item['rooms']);
        unset($item['roomTypePackets']);
    }

    public function prepareRoomTypeImage(&$item){
        $item->roomTypeImages->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at','room_type_id',
            'image_type_id']);
    }

    public function preparePacket(&$roomType, $packetIds)
    {
        $packets = $this->packetServices->getPacketByIds($packetIds)
            ->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
        $this->prepareRatings($roomType, $packets);
        $roomType['packets'] = $packets;
        $roomType->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
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


    public function getRoomType($param)
    {
    }

    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        if ($data) {
            if (!$data->rooms->isEmpty()) {
				$packetIds = $data->roomTypePackets->pluck("packet_id");
                $this->prepareRoomType($data,$packetIds);
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
