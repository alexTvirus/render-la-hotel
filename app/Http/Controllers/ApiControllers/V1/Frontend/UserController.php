<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

use App\Http\Controllers\BaseController;

use App\Services\UserServices;
use App\Tranformers\UserResource\UserDetailResource;
use App\Tranformers\UserResource\UserListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{

    private $userServices;
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
        parent::__construct();
    }
	
	public function index(Request $request)
    {
        $lists = $this->userServices->index($request);
		
		if ($lists instanceof \Illuminate\Pagination\LengthAwarePaginator){
            return (new UserListResource($lists))->additional([
            'totalPage' => $lists->total(),
            'lastPage' => $lists->lastPage(),
            'currentPage' => $lists->currentPage(),
            'perPage' => (int)$lists->perPage(),
			]);

        }
        return (new UserListResource($lists));
	
    }

    public function update(Request $request,$id)
    {
		$validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'phone' => 'required|string|max:20|between:6,20',
        ]);

        if ($validator->fails()) {
            return $this->responseErrorJson("fail", Response::HTTP_CONFLICT, $validator->errors()->first());
        }
		
        $info = $request->only([
            "email",
            "first_name",
            "last_name",
            "phone",
			"birthday",
			"national"
        ]);
		$info['id'] = $id;
        DB::beginTransaction();
        try {
            $entity = $this->userServices->save($info);
            DB::commit();
            return $this->responseJson('success', Response::HTTP_OK, new UserDetailResource($entity));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->responseJson('fail', Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
