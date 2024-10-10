<?php


namespace App\Tranformers\BenefitResource;


use App\Tranformers\ApiResource;

class BenefitListResource extends ApiResource
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
