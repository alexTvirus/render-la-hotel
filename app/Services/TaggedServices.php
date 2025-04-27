<?php


namespace App\Services;


use App\Models\Tagged as TaggedModel;
use Illuminate\Support\Facades\Storage;

class TaggedServices extends BaseServices
{
    private $contentImageServices ;
    public function __construct(TaggedModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $limit = $request->get('limit', TaggedModel::LIMIT_PAGE);
        $query = $this->model;
        return $query->paginate($limit);
    }

    public function show($id)
    {

    }

    public function save(array $attributes)
    {
        if (!empty($attributes['comic_id'])&&!empty($attributes['hashtag_id'])) {
            $entity = $this->model
                ->where('comic_id',$attributes['comic_id'])
                ->where('hashtag_id',$attributes['hashtag_id'])
                ->first();
            if ($entity) {
                $this->model->where('comic_id', $attributes['comic_id'])
                    ->update(['is_main_tag' => 0]);
                $entity->fill($attributes)->save();
                return $entity;
            } else {
                return $this->model->create($attributes);
            }
        } else {
            return null;
        }
    }

    public function deleteByComicIdandHashtagId($comic_id,$hashtags){
        return $this->model->where('comic_id',$comic_id)->whereIn('hashtag_id',$hashtags)->delete();
    }

    public function findByComicIdandHashtagandIsMain($comic_code,$is_main_tag=true){
        $query = TaggedModel::query();
        $query = $query->whereHas('comic',function ($query)use ($comic_code){
            $query->where('comic_code',$comic_code);
        })->where('is_main_tag',$is_main_tag);
        return $query->first();
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
