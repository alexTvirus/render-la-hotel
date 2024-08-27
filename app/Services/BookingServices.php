<?php


namespace App\Services;


use App\Enums\BookingStatus;
use App\Models\Booking as BookingModel;
use Illuminate\Support\Facades\Storage;
use phpseclib3\File\ASN1\Maps\Extension;
use stdClass;

class BookingServices extends BaseServices
{
    private $roomTypePacketServices;
    private $roomServices;
    private $roomBookingServices;
    private $paymentServices;

    public function __construct(BookingModel $model, RoomTypePacketServices $roomTypePacketServices,
                                RoomServices $roomServices,
                                RoomBookingServices $roomBookingServices,
                                PaymentServices $paymentServices)
    {
        parent::__construct($model);
        $this->roomTypePacketServices = $roomTypePacketServices;
        $this->roomServices = $roomServices;
        $this->roomBookingServices = $roomBookingServices;
        $this->paymentServices = $paymentServices;
    }

    public function index($request)
    {
        $query = $this->model;
        $customerId = $request->get('customer_id');
        if ($customerId && !empty($customerId)) {
            $query = $query->with('customer', function ($query) use ($customerId) {
                $query->where('id', $customerId);
            });
        }
        return $query->get();
    }

    public function getBookingByNotAvailble($param)
    {
        $from_time = $param['checkin_at'];
        $to_time = $param['checkout_at'];
        $query = $this->model;
        $query = $query->whereHas('bookingStatus', function ($query) {
            $query->where('name', '!=', BookingStatus::getKey(BookingStatus::CANCEL));
        });
        $query->where(function ($query) use ($from_time, $to_time) {
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
        return $query->get();
    }

    public function makeCheckout($request)
    {
        // todo: tính lại total price dựa vào giá phòng, và giá packet
        // todo: nhận về id room type, list id packet, tìm ra các room ứng với dữ liệu trên
        // nếu số lượng room search nhỏ hơn số lượng room cần đăt , thì báo lỗi
        // 1. tạo 1 record booking với thông tin booking
        // nếu dữ liệu của paymentamount bằng với total price, thì set status là complete
        // nhỏ hơn thì set partialy paid , nếu paymentamount = 0 thì set pending
        // 2. tạo reocrd bookin_room với id booking và id room
        // 3. tạo record payment với id booking

        $totalPrice = 10000;
        $roomType = $request["room.id"];
        $numberRoom = count($request["packets"]);
        $packets = [];
        $paymentAmount = $request["payment_amount"];
        $request["packets"]->each(function ($item, $index) {
            $packets[] = $item->id;
        });

        $isStopBooking = false;

        $booking = [];

        $roomTypepackets = $this->roomTypePacketServices->getRoomTypePacketByRoomTypeAndPacket($roomType, $packets);

        $rooms = $this->roomServices->getRoomByRoomTypePacket($roomTypepackets);

        if (($rooms->count() < $numberRoom) || $isStopBooking) {
            throw new Exception("room not available");
        }
        if ($paymentAmount == 0) {
            $booking["status"] = BookingStatus::PENDING;
        } else if ($totalPrice > $paymentAmount) {
            $booking["status"] = BookingStatus::PARTIALLY_PAID;
        } else if ($paymentAmount >= $totalPrice) {
            $booking["status"] = BookingStatus::COMPLETED;
        }


        $booking["checkin_at"] = $request["checkin_at"];
        $booking["checkout_at"] = $request["checkout_at"];
        $booking["total_price"] = $totalPrice;
        $booking["number_guests"] = $request["guests"];

        $booking = $this->save($booking);

        if (!$booking || $isStopBooking) {
            throw new Exception("booking not available");
        }

        $roomBookings = collect();
        $rooms->each(function ($room) use ($booking, &$roomBookings) {
            $roomBooking = [
                "booking_id" => $booking->id,
                "room_id" => $room->id
            ];

            $roomBooking = $this->roomBookingServices->save($roomBooking);
            if (!$roomBooking) {
                $isStopBooking = true;
                return false;
            }
            $roomBookings->push($roomBooking);
        });

        if ($isStopBooking) {
            throw new Exception("booking not available");
        }

        $payment = [
            "payment_date" => $request["payment_date"] || "",
            "payment_method" => $request["payment_method"] || "",
            "description" => $request["description"] || "",
            "payment_amount" => $request["payment_amount"] || "",
            "address" => $request["address"] || "",
            "email" => $request["email"] || "",
            "city" => $request["city"] || "",
            "state" => $request["state"] || "",
            "post_code" => $request["post_code"] || ""
        ];
        $payment = $this->paymentServices->save($payment);

        if (!$payment || $isStopBooking) {
            throw new Exception("payment not available");
        }

        $booking->payment = $payment;
        $booking->rooms = $roomBookings;

        return $booking;
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
