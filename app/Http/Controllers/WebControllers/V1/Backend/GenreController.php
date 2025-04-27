<?php

namespace App\Http\Controllers\WebControllers\V1\Backend;

use App\Http\Controllers\BaseController;
use App\Services\ChapterServices;
use App\Services\GenreServices;
use Illuminate\Http\Request;

class GenreController extends BaseController
{
    private $genreServices;

    public function __construct(GenreServices $genreServices)
    {
        $this->genreServices = $genreServices;
        parent::__construct();
    }

}
