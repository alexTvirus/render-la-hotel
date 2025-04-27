<?php


namespace App\Services;


use App\Models\ContentImage as ContentImageModel;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ContentImageServices extends BaseServices
{
    private  $url_update_link = "https://api-update-img-render.onrender.com/save-content-image";

    public function __construct(ContentImageModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
        $limit = $request->get('limit', ContentImageModel::LIMIT_PAGE);
        $query = $this->model;
        return $query->paginate($limit);
    }

    public function findByChapterId($request, $id)
    {
        $limit = $request->get('limit', 1);
        $query = ContentImageModel::query();
        $query = $query->where('chapter_id', $id)->orderBy('id', 'asc');
        return $query->paginate($limit);
    }

    public function show($chapterNumber)
    {

    }

    public function save(array $attributes)
    {
        $entity = null;
        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();
            }
        } else {
            $entity = $this->model->create($attributes);
        }
        if($entity){
            $result['id'] = $entity->id;
            $result['link_img'] = $this->getGGId($entity->link_img);
            $json = json_encode($result);
            Http::withBody($json, 'application/json')
                ->post($this->url_update_link);
        }
        return $entity;
    }

    public function uploadGGDrive($request, &$comic)
    {
        $file = [];
        $file['link_img']['file'] = $request->file('link_img');

        $driveService = Storage::disk('google');

        $newPermission = app()->make('googlePermission');

        $folderId = app()->make('googleFolderId');

        $file['link_img']['url'] = [];

        if (isset($file['link_img']['file'])) {
            foreach ($file['link_img']['file'] as $itemImg) {
                $fileToUpload = $this->postGGDrive($driveService, $itemImg, $folderId);
                if ($fileToUpload) {
                    $driveService->permissions->create($fileToUpload->id, $newPermission);
                    $file['link_img']['url'][] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
                }

            }
        }


        return $file;
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        // xoa img tren ggdrive
        $result = collect($entity)->only(['link_img','link_img_backup']);
        $this->deteleGGDrive($result->toArray());
        return !empty($entity) ? $entity->delete() : null;
    }


}
