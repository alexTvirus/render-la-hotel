<?php


namespace App\Tranformers\BookingResource;


use App\Tranformers\ApiResource;

class BookingListResource extends ApiResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $lists = collect();
        $this->resource->each(function ($guide, $index) use (&$lists) {
            $lists->push([
                'index' => $index + 1,
                'id' => $guide->id,
                'checkout_at' => $guide->checkout_at ?? "",
                'checkin_at' => $guide->checkin_at ?? "",
                'total_price' => $guide->total_price ?? 0,
                'number_guests' => $guide->number_guests ?? 0,
                'status' => $guide->status ?? '1',
                'cancel_reason' => $guide->cancel_reason ?? null,
                'canCancel' => true,
				'tour' => $guide->tour,
                'rooms' => $guide->rooms,
                "payments" => $guide->payments
            ]);
        });
        

        return $lists->toArray();
    }
}
