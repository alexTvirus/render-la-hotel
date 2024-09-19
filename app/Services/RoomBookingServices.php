<?php


namespace App\Services;


use App\Models\RoomBooking as RoomBookingModel;
use Illuminate\Support\Facades\Storage;
use App\Enums\BookingStatus;
class RoomBookingServices extends BaseServices
{

    public function __construct(RoomBookingModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
    }
	
	public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }

    public function checkTourIsBooked($room_type_packet_id)
    {
		 $query = $this->model
            ->join('bookings', function ($join){
                $join->on('bookings.id', '=', 'room_booking.booking_id')
                    ->where("bookings.status",BookingStatus::COMPLETED);

            })
			->where('room_type_packet_id', $room_type_packet_id);

        $data = $query->get();
		
        return !($data->isEmpty());
    }

    public function getNotAvailableByBooking($param)
    {
        $from_time = $param['checkin_at'];
        $to_time = $param['checkout_at'];
        $query = $this->model
            ->join('bookings', function ($join) use ($from_time, $to_time) {
                $join->on('bookings.id', '=', 'room_booking.booking_id')
                    ->where(function ($query) use($from_time, $to_time){
                        $query->orwhere(function ($query) use ($from_time, $to_time) {
                            $query
                                ->whereRaw("bookings.checkin_at <= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $from_time)
                                ->whereRaw("bookings.checkout_at >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $from_time);
                        });
                        $query->orwhere(function ($query) use ($from_time, $to_time) {
                            $query
                                ->whereRaw("bookings.checkin_at >= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $from_time)
                                ->whereRaw("bookings.checkin_at <= STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')", $to_time);
                        });
                    });


            });

        return $query->get();
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
