<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

class Permission extends BaseModel
{
    use HasFactory;
    protected $table = 'permissions';
	protected $fillable = [
        'id', 
        'name',
        'guard_name',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'description',
    ];
}
