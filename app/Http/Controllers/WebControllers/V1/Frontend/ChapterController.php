<?php

namespace App\Http\Controllers\WebControllers\V1\Frontend;

use App\Http\Controllers\BaseController;
use App\Models\ContentImage;
use App\Services\ChapterServices;
use App\Services\ComicServices;
use App\Services\ContentImageServices;
use Illuminate\Http\Request;

class ChapterController extends BaseController
{
    private $chapterServices;
    private $contentImageServices;
    private $comicServices;

    public function __construct(ChapterServices $chapterServices, ContentImageServices $contentImageServices, ComicServices $comicServices)
    {
        $this->comicServices = $comicServices;
        $this->contentImageServices = $contentImageServices;
        $this->chapterServices = $chapterServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('Frontend.pages.comics.index');
    }

    public function show(Request $request,$slug1, $comic_code,$slug2, $id)
    {
        $comic_code = $request->get('comic_code');
        $comic = $this->chapterServices->findByComicCodeAndChapterId($comic_code, $id);
        if ($comic) {
            $relations = $this->comicServices->getRelationComic($comic_code);
            $contentImages = $comic ? $this->contentImageServices->findByChapterId($request, $comic->id) : [];
            $comic ?->with('nextChapter', 'prvChapter');
            return view('Frontend.pages.chapters.index', compact('comic', 'contentImages', 'relations', 'comic_code'));
        } else {
            return redirect()->back();
        }

    }

}
