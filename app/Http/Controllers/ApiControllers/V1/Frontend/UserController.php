<?php

namespace App\Http\Controllers\ApiControllers\V1\Frontend;

use App\Http\Controllers\BaseController;

use App\Services\UserServices;
use App\Tranformers\UserResource\UserDetailResource;
use App\Tranformers\UserResource\UserListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{

    private $userServices;
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
        parent::__construct();
    }

    public function update(Request $request,$id)
    {
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
