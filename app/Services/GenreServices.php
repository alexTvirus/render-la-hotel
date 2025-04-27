<?php


namespace App\Services;


use App\Models\Genre as GenreModel;

class GenreServices extends BaseServices
{
    public function __construct(GenreModel $model)
    {
        parent::__construct($model);
    }

    public function index($request)
    {
//        $limit = $request->get('limit', GenreModel::LIMIT_PAGE);
        $query = $this->model;
        return $query->get();
    }

    public function show($chapterNumber)
    {
        $data = $this->model->where('chapter_number', $chapterNumber)->with('contentImages')->first();

        return $data;
    }
}
