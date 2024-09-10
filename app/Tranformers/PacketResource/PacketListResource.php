<?php


namespace App\Tranformers\PacketResource;


use App\Tranformers\ApiResource;

class PacketListResource extends ApiResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
