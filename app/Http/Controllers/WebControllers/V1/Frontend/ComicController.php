<?php

namespace App\Http\Controllers\WebControllers\V1\Frontend;

use App\Http\Controllers\BaseController;
use App\Services\ComicServices;
use App\Services\HashtagServices;
use App\Services\TaggedServices;
use Illuminate\Http\Request;

class ComicController extends BaseController
{
    private $comicServices;
    private $hashtagServices;
    public function __construct(ComicServices $comicServices, HashtagServices $hashtagServices)
    {
        $this->hashtagServices= $hashtagServices;
        $this->comicServices = $comicServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $comics = $this->comicServices->index($request);
        $param = ($request->except(['page']));
        return view('Frontend.pages.comics.search',compact('comics','param'));
    }

    public function searchByhashTag(Request $request,$hashtag)
    {
        $request['hashtag'] = $hashtag;
        $comics = $this->comicServices->index($request);
        return view('Frontend.pages.comics.search',compact('comics'));
    }

    public function show(Request $request,$slug)
    {
        $comic_code = $request->get('comic_code');
        $relations = $this->comicServices->getRelationComic($comic_code);
        $main_tag = $this->hashtagServices->findByComicIdandIsMain($comic_code);
        $comic = $this->comicServices->show($comic_code);
        return view('Frontend.pages.comics.detail',compact('comic','relations','main_tag'));
    }
}
