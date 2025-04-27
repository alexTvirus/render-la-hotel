<?php

namespace App\Http\Controllers\AjaxControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Http\Requests\ApiChapterRequest;
use App\Services\ChapterServices;
use App\Services\ComicServices;
use App\Tranformers\ChapterResource\ChapterDetailResource;
use App\Tranformers\ChapterResource\ChapterListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChapterController extends BaseController
{
    private $chapterServices;
    private $comicServices;
    public function __construct(ChapterServices $chapterServices,ComicServices $comicServices)
    {
        $this->comicServices = $comicServices;
        $this->chapterServices = $chapterServices;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $comic = $this->chapterServices->index($request);
       return (new ChapterListResource($comic))->additional([
            'total' => $comic->total(),
            'lastPage' => $comic->lastPage(),
            'currentPage' => $comic->currentPage(),
            'perPage' => (int)$comic->perPage(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApiChapterRequest $request)
    {
        $comic = $request->only([
            'publish_at',
            'free_at',
            'status',
            'chapter_number',
            'next_chapter_id',
            'prv_chapter_id',
            'comic_id',
            'chapter_name',
        ]);

        try{
            $this->chapterServices->uploadGGDrive($request,$comic);
        }catch(\Exception $e){
            return $this->responseJson( trans('chapter.msg_content.msg_edit_fail'),500,$e->getMessage());
        }

        return $this->chapterServices->save($request,$comic);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->responseJson('ok',200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,string $comic_code,string $id)
    {
        $chapters = $this->chapterServices->show($id);
        return new ChapterDetailResource($chapters);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,string $comic_code, string $id)
    {
        $comic = $request->only([
            'publish_at',
            'free_at',
            'status',
            'chapter_number',
            'next_chapter_id',
            'prv_chapter_id',
            'comic_id',
            'chapter_name',
            'content_images_id',
            'id'
        ]);
        $comic['comic_code'] = $comic_code;

        try{
            $this->chapterServices->uploadGGDrive($request,$comic);
        }catch(\Exception $e){
            return $this->responseJson( trans('chapter.msg_content.msg_edit_fail'),500,$e->getMessage());
        }

        return $this->chapterServices->save($request,$comic);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $comic_code, string $id)
    {
        $result = $this->chapterServices->delete($id);
        if (!$result) {
            return $this->responseJson( trans('chapter.msg_content.msg_delete_fail'),500,'');
        }
        return $this->responseJson( trans('chapter.msg_content.msg_delete_success'),200,['redirect'=>route('comics.edit',['code'=>$comic_code])]);
    }
}
