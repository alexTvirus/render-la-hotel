<?php

namespace App\Http\Controllers\WebControllers\V1\Frontend;

use App\Http\Controllers\BaseController;
use App\Services\ComicServices;
use App\Services\GenreServices;
use Google_Service_Drive_DriveFile;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class LandingController extends BaseController
{
    private $comicService;
    private $genreService;
    public function __construct(ComicServices $comicService, GenreServices $genreService)
    {
        $this->genreService = $genreService;
        $this->comicService = $comicService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $genres = $this->genreService->index($request);
        $comics = $this->comicService->index($request);
        $param = ($request->except(['page']));
        return view('Frontend.pages.landing.index',compact('genres','comics','param'));
    }


}
