<?php


namespace App\Services;


use App\Models\Amenity;
use App\Models\BaseModel;
use App\Models\PacketImage;
use App\Models\Rating;
use Illuminate\Support\Facades\Storage;

class ImageServices extends BaseServices
{
    public function __construct(BaseModel $model)
    {
        parent::__construct($model);
    }

    public function uploadGGDrive($request)
    {
        $file = [];
        $file['link_img']['file'] = $request->file('link_img');

        $driveService = Storage::disk('google');

        $newPermission = app()->make('googlePermission');

        $folderId = app()->make('googleFolderId');

        $fileToUpload = $this->postGGDrive($driveService, $file['link_img']['file'], $folderId);
        if ($fileToUpload) {
            $driveService->permissions->create($fileToUpload->id, $newPermission);
            $file['link_img']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000-rw';
        }


        if (!empty($file['link_img']['url'])) {
            return $file['link_img']['url'];
        }
        return null;
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
