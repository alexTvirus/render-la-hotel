<?php


namespace App\Services;


use App\Models\Benefit as BenefitModel;
use Illuminate\Support\Facades\Storage;

class BenefitServices extends BaseServices
{
    public function __construct(BenefitModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
      $limit = $request->get("limit", "");
        $query = $this->model;
		$query =$query->orderBy('updated_at', 'desc');
         $data = empty($limit) ? ($query->get()) : ($query->paginate($limit));
		return $data;
    }


    public function  getBenefitByPackets($rooms){
        $query = $this->model->whereIn('id', $rooms);
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
