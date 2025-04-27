<?php

namespace App\Http\Controllers\WebControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\ComicServices;
use App\Services\GenreServices;
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
        return view('Backend.pages.dashboard.index');
    }
}
