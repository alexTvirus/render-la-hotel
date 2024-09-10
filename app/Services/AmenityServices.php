<?php


namespace App\Services;


use App\Models\Amenity;
use App\Models\Rating;
use Illuminate\Support\Facades\Storage;

class AmenityServices extends BaseServices
{
    public function __construct(Amenity $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
    }


    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }


    public function save(array $attributes)
    {
        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();
                return $entity;
            } else {
                return null;
            }
        } else {
            $entity = $this->model->create($attributes);
            return $entity;
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
