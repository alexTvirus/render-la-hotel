<?php


namespace App\Services;


use App\Models\Hashtag as HashtagModel;
use Illuminate\Support\Facades\Storage;

class HashtagServices extends BaseServices
{
    private $taggedServices ;
    public function __construct(HashtagModel $model,TaggedServices $taggedServices)
    {
        $this->taggedServices = $taggedServices;
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
            $entity = $this->model->where('id',$attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();
                return $entity;
            } else {
                return null;
            }
        } else {
            $entity=$this->model->create($attributes);
            return $entity;
        }
    }

    public function findByComicIdandIsMain($comic_code,$is_main_tag=true){
        $tagged = $this->taggedServices->findByComicIdandHashtagandIsMain($comic_code);
        if($tagged){
            return $this->show($tagged->hashtag_id);
        }else{
            return null;
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
