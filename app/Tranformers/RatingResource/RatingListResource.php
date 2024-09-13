<?php


namespace App\Tranformers\RatingResource;


use App\Tranformers\ApiResource;

class RatingListResource extends ApiResource
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
        $lists['data'] = collect();
		$roomTypePacketId = 0;
        $this->resource->each(function ($guide,$index) use (&$lists,&$roomTypePacketId) {
			$roomTypePacketId = $guide->room_type_packet_id??null;
            $lists['data']->push([
                'index' => $index+1,
                'id' => $guide->id,
                'comment' => $guide->comment??"",
                'rate' => $guide->rate??0,
                'room_type_packet_id' => $guide->room_type_packet_id??null,
                "customer"=>$guide->customer,
                "created_at" => $guide->created_at
            ]);
        });
        $avg = $this->resource->avg('rate');
        $lists['avg'] = number_format((float) $avg, 2, '.', ''); 
		$lists['room_type_packet_id'] = $roomTypePacketId;
		
        return $lists->toArray();
    }
}
