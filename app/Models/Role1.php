<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;
use Illuminate\Support\Facades\DB;

class Role extends BaseRole
{
    use HasFactory;
    protected $table = 'roles';

    protected $fillable = [
        'id', 
        'name',
        'guard_name',
        'created_at',
        'updated_at',
        'status',
        'created_by',
        'updated_by',
        'description',
        'sort_order',
    ];


}
