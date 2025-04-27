<?php


namespace App\Services;


use App\Models\ContentImage as ContentImageModel;
use App\Models\SummaryContent;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Storage;

class SummaryContentServices extends BaseServices
{
    public function __construct(SummaryContent $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $limit = $request->get('limit', SummaryContent::LIMIT_PAGE);
        $query = $this->model;
        return $query->paginate($limit);
    }

    public function show($chapterNumber)
    {

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
            return $this->model->create($attributes);
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        // xoa img tren ggdrive
        $result = collect($entity)->only(['link_img']);
        $this->deteleGGDrive($result->toArray());
        return !empty($entity) ? $entity->delete() : null;
    }


}
