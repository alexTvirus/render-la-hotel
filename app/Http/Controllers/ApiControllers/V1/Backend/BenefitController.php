<?php

namespace App\Http\Controllers\ApiControllers\V1\Backend;

use App\Http\Controllers\BaseController;

use App\Services\BenefitServices;
use App\Tranformers\BenefitResource\BenefitDetailResource;
use App\Tranformers\BenefitResource\BenefitListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BenefitController extends BaseController
{

    private $benefitServices;

    public function __construct(BenefitServices $benefitServices)
    {
        $this->benefitServices = $benefitServices;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $lists = $this->benefitServices->index($request);
        if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return (new BenefitListResource($lists))->additional([
                'totalPage' => $lists->total(),
                'lastPage' => $lists->lastPage(),
                'currentPage' => $lists->currentPage(),
                'perPage' => (int)$lists->perPage(),
            ]);

        }
        return (new BenefitListResource($lists));
    }
	
	  public function show(Request $request, $id)
    {
        $entity = $this->benefitServices->show($id);
        if ($entity)
            return new BenefitDetailResource($entity);
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
            $entity = $this->benefitServices->save($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new BenefitDetailResource($entity);
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
            $entity = $this->benefitServices->save($info);
            if (empty($entity)) {
                DB::rollBack();
                return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);
            }
            DB::commit();
            return new BenefitDetailResource($entity);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
	
	 public function delete(Request $request, $id)
    {
        $entity = $this->benefitServices->delete($id);
        if ($entity)
            return $this->responseJson('success', Response::HTTP_OK, []);
        else
            return $this->responseJson('fail', Response::HTTP_FAILED_DEPENDENCY, []);

    }
}
