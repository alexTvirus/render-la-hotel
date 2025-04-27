<?php

namespace App\Http\Controllers\WebControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\ChapterServices;
use App\Services\ComicServices;
use App\Services\HashtagServices;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ComicRequest;
class ComicController extends BaseController
{
    private $comicService;
    private $chapterServices;
    private $hashtagServices;
    public function __construct(ComicServices $comicService,
                                ChapterServices $chapterServices,
    HashtagServices $hashtagServices)
    {
        $this->hashtagServices = $hashtagServices;
        $this->chapterServices = $chapterServices;
        $this->comicService = $comicService;
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request['isBackend'] = true;
        $request['loading_hashtag']= true;
        $request['loading_tagged']= true;
        $comics = $this->comicService->index($request);
        $comics = $this->comicService->prepareHashtags($comics);
        $param = ($request->except(['page']));
        return view('Backend.pages.comics.list', compact('comics','param'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hashtags = $this->hashtagServices->index($request);
        return view('Backend.pages.comics.add',compact('hashtags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComicRequest $request)
    {
        $comic = $request->only([
            'comic_name',
            'bg_color',
            'tranfer_color',
            'summary_contents',
            'tagged'
        ]);
        try {
            $this->comicService->uploadGGDrive($request,$comic);
        }catch (\Exception $e){
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['comic' => $comic]);
        }

        DB::beginTransaction();
        try {
            $result = $this->comicService->save($comic);
            DB::commit();

            $request->session()->flash('msgSuccess', trans('comic.msg_content.msg_add_success'));
            return redirect()->route('comics.edit', ['code' => $result->comic_code]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['comic' => $comic]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,string $id)
    {
        $comic = $this->comicService->show($id);
        $chapters = $this->chapterServices->findByComics($request,$comic->id);
        $param= $request->except(['page']);
        $hashtags = $this->hashtagServices->index($request);
        return view('Backend.pages.comics.edit', compact('comic','chapters','param','hashtags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comic = $request->only([
            'comic_name',
            'bg_color',
            'tranfer_color',
            'summary_contents',
            'tagged'
        ]);
        $comic['comic_code'] = $id;

        try {
            $this->comicService->uploadGGDrive($request,$comic);
        }catch (\Exception $e){
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['comic' => $comic]);
        }

        DB::beginTransaction();
        try {

            $result = $this->comicService->save($comic);
            DB::commit();

            $request->session()->flash('msgSuccess', trans('comic.msg_content.msg_edit_success'));
            return redirect()->route('comics.edit', ['code' => $result->comic_code]);
        } catch (\Exception $e) {
            DB::rollback();
            $request->session()->flash('msgFail', $e->getMessage());
            return back()->with(['comic' => $comic]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $result = $this->comicService->delete($id);
        if (!$result) {
            $request->session()->flash('msgFail', trans('comic.msg_content.msg_delete_fail'));
        }
        return redirect()->route('comics.list') ;
    }
}
