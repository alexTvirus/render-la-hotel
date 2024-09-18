<?php


namespace App\Tranformers\DashBoardResource;


use App\Tranformers\ApiResource;

class DashBoardDetailResource extends ApiResource
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
