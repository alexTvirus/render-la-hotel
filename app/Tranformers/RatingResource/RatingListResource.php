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
        return $this->resource->toArray();
    }
}
