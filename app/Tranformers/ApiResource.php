<?php

namespace App\Tranformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ApiResource extends JsonResource
{
    public $with = [
        'code' => Response::HTTP_OK,
        'message' => 'success'
    ];
}
