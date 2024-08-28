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

    public function index($customerId, $request)
    {
        $limit = $request->get("limit", BookingModel::LIMIT_PAGE);
        $query = $this->model;

        if ($customerId && !empty($customerId)) {
            $query = $query->where("customer_id", $customerId);
//            $query->with('customer', function ($query) use ($customerId) {
//                $query->where('id', $customerId);
//            });
        }
        $query->with("payments", function ($query) {
            $query->select("payments.id","payments.payment_date",
                "payments.payment_method","payments.description",
            "payments.payment_amount","payments.booking_id");
        });

        $query->with("rooms",function ($query){
            $query->select("rooms.id","rooms.room_view",
                "rooms.room_number","rooms.room_type_packet_id");
        });

        return $query->select("id","checkout_at","checkin_at","total_price"
        ,"number_guests","status")->paginate($limit);
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
        $roomType = $request["room"]["id"];
        $numberRoom = count($request["packets"]);
        $packets = [];
        $paymentAmount = $request["payment"]["payment_amount"] ?? 0;
        foreach ($request["packets"] as $item) {
            $packets[] = $item["id"];
        }

        $isStopBooking = false;

        $booking = [];

        $hashCheck = [];

        // tạo dữ liệu số lượng packet ứng với packet
        foreach ($packets as $packet) {
            if (isset($hashCheck[$packet])) {
                $hashCheck[$packet] += 1;
            } else {
                $hashCheck[$packet] = 1;
            }
        }

        $roomAvailable = collect();

        foreach ($hashCheck as $key => $value) {
            // lấy ra roomtypepacket ứng với packet roomtype
            $roomTypePackets = $this->roomTypePacketServices->getRoomTypePacketByRoomTypeAndPacket($roomType, $key)->pluck("id");
            if (count($roomTypePackets) < 1) {
                $isStopBooking = true;
                break;
            }
            // điều kiện booking về ngày check in phải thõa
            $bookings = $this->getBookingByNotAvailble($request)->pluck("id");
            // lấy ra room thõa đk booking, và thõa đk room type
            $rooms = $this->roomServices->getRoomByRoomTypePacketsAndBooking($roomTypePackets, $bookings);
            // nếu số room tìm đc nhỏ hơn số room muốn đặt thì lỗi
            if ($rooms->count() < $value) {
                $isStopBooking = true;
                break;
            } else {
                $rooms = $rooms->take($value);
                $roomAvailable = $roomAvailable->merge($rooms);
            }
        }


        if ($isStopBooking) {
            throw new \Exception("room not available");
        }


        if ($paymentAmount == 0) {
            $booking["status"] = BookingStatus::PENDING;
        } else if ($totalPrice > $paymentAmount) {
            $booking["status"] = BookingStatus::PARTIALLY_PAID;
        } else if ($paymentAmount >= $totalPrice) {
            $booking["status"] = BookingStatus::COMPLETED;
        }

        // todo: thêm custom id bằng user đang login
        $booking["customer_id"] = 1;
        $booking["checkin_at"] = $request["checkin_at"];
        $booking["checkout_at"] = $request["checkout_at"];
        $booking["total_price"] = $totalPrice;
        $booking["number_guests"] = $request["guests"];

        $booking = $this->save($booking);

        if (!$booking || $isStopBooking) {
            throw new \Exception("booking not available");
        }

        $roomBookings = collect();

        // các room thõa đk có thể đặt phòng
        $roomAvailable->each(function ($room) use ($booking, &$roomBookings) {
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
            throw new \Exception("booking not available");
        }

        $payment = [
            "booking_id" => $booking->id,
            "payment_date" => $request["payment"]["payment_date"] ?? "",
            "payment_method" => $request["payment"]["payment_method"] ?? "",
            "description" => $request["payment"]["description"] ?? "",
            "payment_amount" => $request["payment"]["payment_amount"] ?? "",
            "address" => $request["payment"]["address"] ?? "",
            "email" => $request["payment"]["email"] ?? "",
            "city" => $request["payment"]["city"] ?? "",
            "state" => $request["payment"]["state"] ?? "",
            "post_code" => $request["payment"]["post_code"] ?? ""
        ];
        $payment = $this->paymentServices->save($payment);

        if (!$payment || $isStopBooking) {
            throw new \Exception("payment not available");
        }

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
