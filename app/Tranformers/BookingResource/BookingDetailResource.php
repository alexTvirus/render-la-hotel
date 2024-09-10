<?php


namespace App\Tranformers\BookingResource;


use App\Tranformers\ApiResource;

class BookingDetailResource extends ApiResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        $this->resource->each(function ($guide, $index) {
            $guide['index'] = $index + 1;
            $guide['canCancel'] = true;
        });
        return $this->resource->toArray();
    }
}
