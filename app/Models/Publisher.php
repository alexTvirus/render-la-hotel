<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends BaseModel
{
    protected $table = "publishers";
    protected $fillable =[
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function comics(){
        return $this->hasMany(Comic::class,'publisher_id','id');
    }
}
