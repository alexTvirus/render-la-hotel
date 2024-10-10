<?php


namespace App\Services;


use App\Enums\BookingStatus;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Packet;
use App\Models\Rating;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\RoomType;
use App\Models\RoomTypePacket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DashBoardServices extends BaseServices
{
    public function __construct(Amenity $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $result = collect();
        // todo: tong so phong
        // tong so tour
        $packetServices = app()->make(PacketServices::class);
        $request['manageTour']=1;
		$request['tour']=1;
        $tours = $packetServices->index($request);
        $numberOfTour = $tours;
        $result->put("number_of_tour" , $numberOfTour->count());
//        $result->put(["tours" => $tours]);

        // tong so loai phong
        $roomTypeQuery = RoomType::query();
        $roomType = $roomTypeQuery->get();
        $result->put("number_of_room_type" , $roomType->count());
//        $result->put(["room_types" , $roomType]);

        // tong so tat ca phong dang co
        $roomQuery = Room::query();
        $rooms = $roomQuery->count();
        $result->put("number_of_room" , $rooms);

        // todo:
        // lay tat ca review theo (nam/quy/thang)
        $ratingServices = app()->make(RatingServices::class);
        $allReviews = $ratingServices->index($request)->count();
        $result->put("number_of_all_review" , $allReviews);
        // lay so review 5 sao
        $request['rate'] = 5;
        $fiveRateReviews = $ratingServices->index($request)->count();
        $result->put("number_of_five_rate" , $fiveRateReviews);
        // lay so review 2 sao
        $request['rate'] = 2;
        $twoRateReviews = $ratingServices->index($request)->count();
        $result->put("number_of_two_rate" , $twoRateReviews);

        // todo:
        // lay tat ca phong da dc dat theo (nam/quy/thang)
        $bookingQuery = RoomBooking::query();

        $tourIds = $tours->pluck('room_type_packet_id');
//        $bookingQuery->whereNotIn('room_type_packet_id',$tourIds);
        $booked = $bookingQuery
            ->whereHas('booking' , function ($query) use($tourIds,$request) {
                $query->where('status',BookingStatus::COMPLETED);
                $this->timeCondition($request, $query, app(Booking::class)->getTable());
            })
            ->count();
        $result->put("number_of_room_booked" ,$booked);

        // todo:
        // lay tat ca user dc tao theo (nam/quy/thang)
        $userQuery = User::query();
        $this->timeCondition($request, $userQuery, app(User::class)->getTable());
        $user = $userQuery->active()->count();
        $result->put("number_of_user" , $user);
        // lay tat ca user da verify tao theo (nam/quy/thang)
        $userQuery = User::query();
        $this->timeCondition($request, $userQuery, app(User::class)->getTable());
        $user = $userQuery->active()->verified()->count();
        $result->put("number_of_verified" , $user);

        // todo:
        // lay tat ca tour dc dat theo (nam/quy/thang)
        $bookingTourQuery = Booking::status(BookingStatus::COMPLETED);
        $this->timeCondition($request, $bookingTourQuery, app(Booking::class)->getTable());
        $tourIds = $tours->pluck('room_type_packet_id');
        $booked = $bookingTourQuery
            ->whereHas('roomBookings' , function ($query) use($tourIds) {
                $query->whereIn('room_type_packet_id',$tourIds);
            })
            ->count();
        $result->put("number_of_tour_booked",$booked);

        //todo:
        // du lieu bieu do doanh thu?
        $query_array = $request->query();
        $year = $query_array['year'] ?? Carbon::now()->year;
        $start_date = Carbon::create()->year($year)->month(1)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::create()->year($year)->month(12)->endOfMonth()->format('Y-m-d');
    
        $bookingTourQuery = Booking::status(BookingStatus::COMPLETED);
        //$this->timeCondition($request, $bookingTourQuery, class_basename(Booking::class));
        $finalChartData = $bookingTourQuery
        ->selectRaw('DATE_FORMAT(updated_at, "%m") AS month, 
        SUM(total_price) AS total')
        ->whereRaw(app(Booking::class)->getTable().".updated_at <= STR_TO_DATE(?, '%Y-%m-%d')", $end_date)
        ->whereRaw(app(Booking::class)->getTable().".updated_at >= STR_TO_DATE(?, '%Y-%m-%d')", $start_date)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        $result->put("finalChartData",$finalChartData);

        return $result;
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
