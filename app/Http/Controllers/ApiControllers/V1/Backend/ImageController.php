<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\AmenityServices;
use App\Services\ImageServices;
use App\Tranformers\AmenityResource\AmenityDetailResource;
use App\Tranformers\AmenityResource\AmenityListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ImageController extends BaseController
{

    private $imageServices;

    public function __construct(ImageServices $imageServices)
    {
        $this->imageServices = $imageServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->amenityServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new AmenityListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new AmenityListResource($lists));
    }

    public function show(Request $request, $id)
    {
        $entity = $this->amenityServices->show($id);
        if ($entity)
            return new AmenityDetailResource($entity);
        else
            return $this->responseJson("fail", Response::HTTP_FAILED_DEPENDENCY, []);
    }

    public function store(Request $request)
    {

        try {
            $entity = $this->imageServices->uploadGGDrive($request);
            if (empty($entity)) {
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            return $this->responseJson('success', Response::HTTP_OK, $entity);
        } catch (\Exception $e) {
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function update(Request $request, $id)
    {

    }

    public function delete(Request $request, $id)
    {

    }
}
