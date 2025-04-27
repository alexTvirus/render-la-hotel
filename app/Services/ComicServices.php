<?php


namespace App\Services;

use App\Models\Chapter;
use App\Models\Comic as ComicModel;
use App\Models\Hashtag;
use App\Models\Tagged;
use Carbon\Carbon;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class ComicServices extends BaseServices
{
    private $url_update_link = "https://api-update-img-render.onrender.com/save-comic";

    private $summaryContentServices;
    private $taggedServices;

    public function __construct(ComicModel $model, SummaryContentServices $summaryContentServices, TaggedServices $taggedServices)
    {
        $this->taggedServices = $taggedServices;
        $this->summaryContentServices = $summaryContentServices;
        parent::__construct($model);
    }

    public function index($request)
    {
        $limit = $request->get('limit', ComicModel::LIMIT_PAGE);
        $query = ComicModel::query();
        $hashtag = $request->get('hashtag');
        if ($hashtag && $hashtag != 'null') {
            $query = $query->whereHas('hashtags', function ($query) use ($hashtag) {
                $query->where('hashtags.slug', $hashtag);
            });
        }
        if (!empty($request['loading_hashtag'])) {
            $query = $query->with('hashtags');
        }

        if (!empty($request['loading_tagged'])) {
            $query = $query->with('taggeds');
        }

        $keyword = $request->get('keyword');
        if ($keyword && $keyword != 'null') {
            $query = $query->search($keyword);
        }

        $query = $query->with('chapters', function ($query) {
            $query->orderBy('id', 'asc');
        });

        if(!$request->get('isBackend')){
            $latestChapters = DB::table('chapters')
                ->select('comic_id', DB::raw('MAX(publish_at) as latest_chapter_updated_at'))
                ->groupBy('comic_id');

            $query = $query
                ->joinSub($latestChapters, 'latest_chapters', function ($join) {
                    $join->on('comics.id', '=', 'latest_chapters.comic_id');
                })
                ->orderBy('latest_chapter_updated_at', 'desc');
        }

        $query->orderBy('comics.id', 'desc');

        $query->select('comics.*');

        $data = $query->paginate($limit);

        if (!$data->isEmpty()) {
            // cal diff time
            $data = $this->calDiffTime($data);

            // sort
            $data = $this->sortDataBy($data);

            $data = $this->makeSkeletonColor($data);
        }

        return $data;
    }

    public function makeSkeletonColor($data)
    {
        $data->each(function ($item) {
            $item['skeleton_bg_color'] = str_replace(")", ", 0.3)", $item->bg_color);
        });
        return $data;
    }

    public function calDiffTime($data)
    {
        $now = Carbon::now();
        $data->each(function ($item) use ($now) {



            if ($item ?->chapters->isNotEmpty()){
                $latestItem = $item->chapters->reduce(function ($carry, $item) {
                    if (is_null($carry) || $item['publish_at'] > $carry['publish_at']) {
                        return $item;
                    }
                    return $carry;
                });

                $item['diff_time'] = $now->diffInMinutes($latestItem->publish_at);
                    $item['diff_time_to_sort'] = $now->diffInMinutes($latestItem->publish_at);
                    if ($item['diff_time'] <= 0) {
                        $item['diff_time'] = 'Mới cập nhật';
                    } else if ($item['diff_time'] <= 60) {
                        $item['diff_time'] = 'Cách đây ' . $item['diff_time'] . ' phút';
                    } else if ($item['diff_time'] <= 1440) {
                        $item['diff_time'] = 'Cách đây ' . round(($item['diff_time'] / 60), 0, PHP_ROUND_HALF_DOWN) . ' giờ';
                    } else if ($item['diff_time'] <= 10080) {
                        $item['diff_time'] = 'Cách đây ' . round(($item['diff_time'] / (60 * 24)), 0, PHP_ROUND_HALF_DOWN) . ' ngày';
                    } else if ($item['diff_time'] <= 40320) {
                        $item['diff_time'] = 'Cách đây ' . round(($item['diff_time'] / (60 * 24 * 7)), 0, PHP_ROUND_HALF_DOWN) . ' tuần';
                    } else {
                        $item['diff_time'] = 'Cách đây ' . round(($item['diff_time'] / (60 * 24 * 7 * 4)), 0, PHP_ROUND_HALF_DOWN) . ' tháng';
                    }
                }else{
                $item['diff_time_to_sort'] = 0;
                $item['diff_time'] = 'Mới cập nhật';
            }

            });
        return $data;
    }

    public function sortDataBy($data)
    {
        $data->setCollection(collect($data->items())->sortBy(function ($item) {
            return $item['diff_time_to_sort'];
        }));
        return $data;
    }

    public function prepareHashtags($data)
    {
        foreach ($data as $comic) {
            $idTag = $comic->taggeds->where('is_main_tag', 1)->first();
            if ($idTag) {
                $comic->hashtags->each(function ($item) use ($idTag) {
                    if ($idTag->hashtag_id == $item->id) {
                        $item['is_main_tag'] = true;
                    }
                });
            }
        }
        return $data;
    }

    public function save(array $attributes)
    {
        $entity = null;
        if (!empty($attributes['comic_code'])) {
            $entity = $this->model->where('comic_code', $attributes['comic_code'])->first();
            if ($entity) {
                if ($attributes['bg_color']) {
                    $attributes['tranfer_color'] = "background:linear-gradient(to bottom, rgba({$attributes['bg_color']},0) 2%, rgba({$attributes['bg_color']},0.7) 50%,  rgba({$attributes['bg_color']}))";
                    $attributes['bg_color'] = "rgba({$attributes['bg_color']})";
                } else {
                    $attributes['tranfer_color'] = '';
                }

                $entity->fill($attributes)->save();

                $this->updateSummaryContents($attributes, $entity);
                $this->updateTaggeds($attributes, $entity);

            }
        } else {
            $attributes['comic_code'] = "COMIC-";
            if ($attributes['bg_color']) {
                $attributes['tranfer_color'] = "background:linear-gradient(to bottom, rgba({$attributes['bg_color']},0) 2%, rgba({$attributes['bg_color']},0.7) 50%,  rgba({$attributes['bg_color']}))";
                $attributes['bg_color'] = "rgba({$attributes['bg_color']})";
            } else {
                $attributes['tranfer_color'] = '';
            }

            $entity = $this->model->create($attributes);
            $entity->comic_code = "COMIC-" . $entity->id;
            $entity->save();
            $this->addSummaryContents($attributes, $entity);
            $this->addTaggeds($attributes, $entity);
        }
        if ($entity) {
            $result['id'] = $entity->id;
            $result['link_avatar'] = $this->getGGId($entity->link_avatar);
            $result['link_comic_name'] = $this->getGGId($entity->link_comic_name);
            $result['link_bg'] = $this->getGGId($entity->link_bg);
            $result['link_banner'] = $this->getGGId($entity->link_banner);
            $result['link_comic_small_name'] = $this->getGGId($entity->link_comic_small_name);
            $json = json_encode($result);
            Http::withBody($json, 'application/json')
                ->post($this->url_update_link);
        }
        return $entity;
    }

    public function updateSummaryContents($attributes, $entity)
    {
        $summayContent['id'] = $entity ?->summaryContents ?->id;
        $summayContent['comic_id'] = $entity['id'];
        $summayContent['content'] = $attributes['summary_contents'];
        $this->summaryContentServices->save($summayContent);
    }

    public function addSummaryContents($attributes, $entity)
    {
        $summayContent['comic_id'] = $entity['id'];
        $summayContent['content'] = $attributes['summary_contents'];
        $this->summaryContentServices->save($summayContent);
    }

    public function updateTaggeds($attributes, $entity)
    {
        // lay ra tat cả tagged của entity
        $oldTaggeds = $entity->hashtags->pluck('id')->all();
        $listtemp = [];
        if (isset($attributes['tagged'])) {
            foreach ($attributes['tagged'] as $index => $tagged) {
                if (in_array($tagged, $oldTaggeds)) {
                    $listtemp[] = $tagged;
                } else {
                    $this->taggedServices->save(['comic_id' => $entity->id, 'hashtag_id' => $tagged]);
                }
            }
        }

        // lọc các id cần xóa
        $listdelete = array_diff($oldTaggeds, $listtemp);
        $this->taggedServices->deleteByComicIdandHashtagId($entity->id, $listdelete);
    }


    public function addTaggeds($attributes, $entity)
    {
        if (isset($attributes['tagged'])) {
            foreach ($attributes['tagged'] as $index => $tagged) {
                $this->taggedServices->save(['comic_id' => $entity->id, 'hashtag_id' => $tagged]);
            }
        }

    }

    public function createIdGG($driveService, $folderId, $newPermission, $field_name, $googleUrl, &$comic, &$file)
    {
        $fileToUpload = $this->postGGDrive($driveService, $file[$field_name]['file'], $folderId);
        if ($fileToUpload) {
            $driveService->permissions->create($fileToUpload->id, $newPermission);
            $file[$field_name]['url'] = $googleUrl[0] . $fileToUpload->id . $googleUrl[1];
            $comic[$field_name] = $file[$field_name]['url'];
            $comic[$field_name.'_backup'] = $file[$field_name]['url'];
        }
        return $file;
    }

    public function uploadGGDrive($request, &$comic)
    {
        $file = [];
        $file['link_banner']['file'] = $request->file('link_banner');
        $file['link_video_banner']['file'] = $request->file('link_video_banner');
        $file['link_video_banner_2']['file'] = $request->file('link_video_banner_2');
        $file['link_avatar']['file'] = $request->file('link_avatar');
        $file['link_comic_name']['file'] = $request->file('link_comic_name');
        $file['link_comic_small_name']['file'] = $request->file('link_comic_small_name');
        $file['link_bg']['file'] = $request->file('link_bg');

        $driveService = Storage::disk('google');

        $newPermission = app()->make('googlePermission');
        $folderId = app()->make('googleFolderId');

        $googleUrlImg[0] = "https://lh3.googleusercontent.com/d/";
        $googleUrlImg[1] = "=w1000";

        $googleUrlVideoWebm[0] = "https://drive.usercontent.google.com/download?id=";
        $googleUrlVideoWebm[1] = "&export=download&type=video/webm";

        $googleUrlVideoMov[0] = "https://drive.usercontent.google.com/download?id=";
        $googleUrlVideoMov[1] = "&export=download&type=video/quicktime";

        $this->createIdGG($driveService, $folderId, $newPermission, "link_avatar", $googleUrlImg, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_banner", $googleUrlImg, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_video_banner", $googleUrlVideoWebm, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_video_banner_2", $googleUrlVideoMov, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_comic_name", $googleUrlImg, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_comic_small_name", $googleUrlImg, $comic, $file);
        $this->createIdGG($driveService, $folderId, $newPermission, "link_bg", $googleUrlImg, $comic, $file);

//        $fileToUpload = $this->postGGDrive($driveService, $file['link_avatar']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_avatar']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
//            $comic['link_avatar'] = $file['link_avatar']['url'];
//        }
//
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_comic_name']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_comic_name']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
//            $comic['link_comic_name'] = $file['link_comic_name']['url'];
//        }
//
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_bg']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_bg']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
//            $comic['link_bg'] = $file['link_bg']['url'];
//        }
//
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_banner']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_banner']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
//            $comic['link_banner'] = $file['link_banner']['url'];
//        }
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_video_banner']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_video_banner']['url'] = 'https://drive.usercontent.google.com/download?id=' . $fileToUpload->id . '&export=download';
//            $comic['link_video_banner'] = $file['link_video_banner']['url'];
//        }
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_video_banner_2']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_video_banner_2']['url'] = 'https://drive.usercontent.google.com/download?id=' . $fileToUpload->id . '&export=download';
//            $comic['link_video_banner_2'] = $file['link_video_banner_2']['url'];
//        }
//
//
//        $fileToUpload = $this->postGGDrive($driveService, $file['link_comic_small_name']['file'], $folderId);
//        if ($fileToUpload) {
//            $driveService->permissions->create($fileToUpload->id, $newPermission);
//            $file['link_comic_small_name']['url'] = 'https://lh3.googleusercontent.com/d/' . $fileToUpload->id . '=w1000';
//            $comic['link_comic_small_name'] = $file['link_comic_small_name']['url'];
//        }

        return $file;
    }

    public function findByComicCode($comic_code)
    {
        return $this->model->where('comic_code', $comic_code)->first();
    }

    public function getAllComics()
    {
        $data = ComicModel::query();

        $data = $data
            ->select('slug', 'id', 'comic_name');
        $data = $data->with('chapters', function ($data) {
            $data->select('comic_id', 'slug', 'chapter_name', 'id');
        })->get();
        return $data;
    }

    public function show($comicCode)
    {
        $data = ComicModel::query();

        $data = $data->with('chapters', function ($query) {
            $query->orderBy('id', 'asc');
        })
            ->with('summaryContents')
            ->with('hashtags')
            ->where('comic_code', $comicCode)->first();
        if (!empty($data)) {
            $now = Carbon::now();
            if ($data ?->chapters ?->last() ?->publish_at){
                $data['diff_time'] = $now->diffInMinutes($data ?->chapters ?->last() ?->publish_at);
                    if ($data['diff_time'] <= 0) {
                        $data['diff_time'] = 'Mới cập nhật';
                    } else if ($data['diff_time'] <= 60) {
                        $data['diff_time'] = 'Cách đây ' . $data['diff_time'] . ' phút';
                    } else if ($data['diff_time'] <= 1440) {
                        $data['diff_time'] = 'Cách đây ' . round(($data['diff_time'] / 60), 0, PHP_ROUND_HALF_DOWN) . ' giờ';
                    } else {
                        $data['diff_time'] = 'Cách đây ' . round(($data['diff_time'] / (60 * 24)), 0, PHP_ROUND_HALF_DOWN) . ' ngày';
                    }
                }else{
                $data['diff_time'] = 'Mới cập nhật';
            }

            $data->chapters->each(function ($item) {
                foreach (Chapter::TIME as $key => $value) {
                    if ($item[$value])
                        $item[$value] = Carbon::parse($item[$value])->tz(config('app.timezone'))->format('d/m/Y');
                }
            });

             $data['skeleton_bg_color'] = str_replace(")", ", 0.3)", $data->bg_color);
        }
        return $data;
    }

    public function getRelationComic($comic_code, $limit = 10)
    {
        // lấy ra các hashtag của comic này
        $comic = $this->findByComicCode($comic_code);
        // nếu ko sợ việc lặp record thì dùng join sẽ nhanh hơn , nhưng trường hợp này , 2 bảng
        // comic và hashtag là n-n với nhau nên sẽ xảy ra lặp >> ko dùng join
//         $hashtags = $comic->hashtags->pluck('id')->toArray();

        // dùng whereHas để lấy ra các hashtags có comic id
        $hashtags = Hashtag::select('id')->whereHas('comics', function ($hashtags) use ($comic) {
            $hashtags->where('comic_id', $comic->id);
        });
        // ở đây ta lấy ra array các id hashtag , thì lúc chạy query dưới sẽ nhanh hơn
        // là tạo 1 câu query dài
        $hashtags = $hashtags->get()->toArray();

        $query = $this->model->whereIn('id', function ($query) use ($hashtags) {
            $query->select('comic_id')->from('taggeds')->whereIn('hashtag_id', $hashtags);
        });

        $data = $query->limit($limit)->get();

        if (!$data->isEmpty()) {
            $data = $this->makeSkeletonColor($data);
        }

        return $data;
    }

    public function delete($comicCode)
    {
        $entity = $this->model
            ->where('comic_code', $comicCode)->first();
        // xoa img tren ggdrive
        $result = collect($entity)->only(['link_bg', 'link_avatar', 'link_comic_name', 'link_banner', 'link_comic_small_name',
            'link_banner_backup','link_bg_backup','link_avatar_backup','link_comic_name_backup','link_comic_small_name_backup']);
        $this->deteleGGDrive($result->toArray());
        return !empty($entity) ? $entity->delete() : null;
    }
}
