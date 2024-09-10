<?php


namespace App\Tranformers\UserResource;


use App\Tranformers\ApiResource;

class UserDetailResource extends ApiResource
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
