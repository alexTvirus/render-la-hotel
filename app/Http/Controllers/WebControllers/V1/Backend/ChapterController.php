<?php

namespace App\Http\Controllers\WebControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\ChapterServices;
use Illuminate\Http\Request;

class ChapterController extends BaseController
{
    private $chapterServices;

    public function __construct(ChapterServices $chapterServices)
    {
        $this->chapterServices = $chapterServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
    }

    public function show($comic_code,$chapterNumber)
    {

    }
}
