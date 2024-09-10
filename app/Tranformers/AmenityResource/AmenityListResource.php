<?php


namespace App\Tranformers\AmenityResource;


use App\Tranformers\ApiResource;

class AmenityListResource extends ApiResource
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
