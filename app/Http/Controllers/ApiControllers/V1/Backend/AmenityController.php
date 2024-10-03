<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\AmenityServices;
use App\Tranformers\AmenityResource\AmenityDetailResource;
use App\Tranformers\AmenityResource\AmenityListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AmenityController extends BaseController
{

    private $amenityServices;

    public function __construct(AmenityServices $amenityServices)
    {
        $this->amenityServices = $amenityServices;
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
        $info = $request->only([
            'name',
            'description',
            'image'
        ]);
        DB::beginTransaction();
        try {
            $entity = $this->amenityServices->save($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new AmenityDetailResource($entity);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function update(Request $request, $id)
    {
        $info = $request->only([
            'name',
            'description',
            'image'
        ]);
        $info['id'] = $id;
        DB::beginTransaction();
        try {
            $entity = $this->amenityServices->save($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new AmenityDetailResource($entity);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
	
	 public function delete(Request $request, $id)
    {
        $entity = $this->amenityServices->delete($id);
        if ($entity)
            return $this->responseJson('success', Response::HTTP_OK, []);
        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);

    }
}
