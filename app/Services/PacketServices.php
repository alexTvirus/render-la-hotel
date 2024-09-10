<?php


namespace App\Services;


use App\Models\Packet as PacketModel;
use Illuminate\Support\Facades\Storage;

class PacketServices extends BaseServices
{

    public function __construct(PacketModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $query = $this->model;
        return $query->get();
    }


    public function  getPacketByIdsWithBenefits($ids){
        $query = $this->model->whereIn('id', $ids)
            ->with(['benefits'=> function($query) {
                $query->select('benefits.id', 'benefits.name','benefits.price','benefits.description');
            }]);

        return $query->get();
    }

    public function  getPacketByIds($ids){
        $query = $this->model->whereIn('id', $ids);
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
