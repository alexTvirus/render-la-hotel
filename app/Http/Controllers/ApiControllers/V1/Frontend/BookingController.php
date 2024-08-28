<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

use App\Http\Controllers\BaseController;

use App\Services\BookingServices;
use App\Services\RoomServices;
use App\Tranformers\BookingResource\BookingDetailResource;
use App\Tranformers\BookingResource\BookingListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class BookingController extends BaseController
{
    private $bookingServices;


    public function __construct(BookingServices $bookingServices)
    {
        $this->bookingServices = $bookingServices;

        parent::__construct();
    }

    public function index(Request $request,$customerId)
    {
        $lists = $this->bookingServices->index($customerId,$request);
        return (new BookingListResource($lists))->additional([
            'total' => $lists->total(),
            'lastPage' => $lists->lastPage(),
            'currentPage' => $lists->currentPage(),
            'perPage' => (int)$lists->perPage(),
        ]);
    }

    public function store(Request $request){
		// todo : neu that bai tra ve ly do that bai
        $info = $request->only([
            'packets',
            'room',
            'checkout_at',
            'checkin_at',
            'guests',
            'payment.payment_method',
            'payment.payment_date',
            'payment.description',
            'payment.payment_amount',
            'payment.address',
            'payment.email',
            'payment.city',
            'payment.state',
            'payment.post_code',
        ]);
        DB::beginTransaction();
        try {
            $entity = $this->bookingServices->makeCheckout($info);
            DB::commit();
            return $this->responseJson('success', Response::HTTP_OK, new BookingDetailResource($entity));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseJson($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR,["status"=>4]);
        }
    }
}
