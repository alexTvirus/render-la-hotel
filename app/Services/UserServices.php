<?php


namespace App\Services;



use App\Models\User;
use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class UserServices extends BaseServices
{
    public $model;
    public function __construct(User $model)
    {
        parent::__construct(new Model());
        $this->model = $model;
    }

    public function index($request)
    {
		$limit = $request->get("limit", "");
		$query_array = $request->query();
		$isUser = $query_array['isUser'] ?? "";
        $query = $this->model;
		
		if (!empty($isUser)) {
            $query = $query->whereHas("roles", function($q) {
				$q->whereNotIn("name", ["admin"]);
			});
        }
		
        $relations = $request->get("loadRelation", []);
        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }
		$query = $query->orderBy('users.updated_at', 'desc');
        $data = empty($limit) ? ($query->get()) : ($query->paginate($limit));
		//$this->model->where("ef",32)->get();
		return $data;
    }

	public function getAllWishlists($request)
    {
		$limit = $request->get('limit', RoomTypeModel::LIMIT_PAGE);
        $query_array = $request->query();
		$roomTypes = json_decode($roomTypes, TRUE);

        $query = $this->model;
        $relations = $request->get("loadRelation", []);
        if (!empty($relations)) {
            foreach ($relations as $key => $value) {
                $query = $query->with($value);
            }
        }
		$query = $query->orderBy('users.updated_at', 'desc');
        $data = empty($limit) ? ($query->get()) : ($query->paginate($limit));
		return $data;
    }

	 public function saveWishlists($request,array $attributes)
    {
		$room_types = $attributes['room_types'] ?? "";
		$entity = $this->getCurrentUser();
        if(!empty($entity) && !empty($room_types)){
            $user = $entity->whereHas("wishlists",function ($query)use($room_types){
                $query->where('room_type_id',$room_types);
            })->first();
            if(!empty($user)){
                $entity->roomTypes()->detach($room_types);
            }else{
                $entity->roomTypes()->attach($room_types);
            }
        }
		return $entity;
    }


    public function show($id)
    {
        $data = $this->model->where('id', $id)->first();
        return $data;
    }

    public function getUserByEmail($email){
        $data = $this->model->where('email', $email)->first();
        return $data;
    }

    public function save(array $attributes)
    {
        if (!empty($attributes['id'])) {
            $entity = $this->model->where('id', $attributes['id'])->first();
            if ($entity) {
                $entity->fill($attributes)->save();
                return $entity;
            } else {
                return null;
            }
        } else {
            $entity = $this->model->create($attributes);
			$entity->assignRole("user");
            return $entity;
        }
    }

    public function delete($id)
    {
        $entity = $this->model
            ->where('id', $id)->first();
        return !empty($entity) ? $entity->delete() : null;
    }
}
