<?php

namespace App\Http\Controllers\AjaxControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\HashtagServices;
use App\Services\TaggedServices;
use App\Tranformers\ChapterResource\ChapterDetailResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HashtagController extends BaseController
{
    private $taggedServices;

    public function __construct(TaggedServices $taggedServices)
    {
        $this->taggedServices = $taggedServices;
        parent::__construct();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,string $comic_id, string $hashtag_id)
    {
        $attributes['comic_id'] = $comic_id;
        $attributes['hashtag_id'] = $hashtag_id;
        $attributes['is_main_tag'] = 1;
        DB::beginTransaction();
        try {
            $result =$this->taggedServices->save($attributes);
            DB::commit();

            return $this->responseJson( trans('chapter.msg_content.msg_edit_success'),200,'');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseJson( trans('chapter.msg_content.msg_edit_fail'),500,$e->getMessage());
        }
    }

}
