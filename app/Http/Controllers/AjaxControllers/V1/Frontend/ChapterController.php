<?php

namespace App\Http\Controllers\AjaxControllers\V1\Frontend;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ApiChapterRequest;
use App\Services\ChapterServices;
use App\Services\ComicServices;
use App\Services\ContentImageServices;
use App\Tranformers\ContentImageResource\ContentImageListResource;
use App\Tranformers\ChapterResource\ChapterListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChapterController extends BaseController
{
    private $contentImageServices;
    public function __construct(ContentImageServices $contentImageServices)
    {
        $this->contentImageServices = $contentImageServices;
        parent::__construct();
    }


    public function show(Request $request,$comic_code,$id)
    {
        $contentImages = $this->contentImageServices->findByChapterId($request,$id);
        return new ContentImageListResource($contentImages);
    }

}
