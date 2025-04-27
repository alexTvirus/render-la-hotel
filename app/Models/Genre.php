<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends BaseModel
{
    protected $table = "genres";
    protected $fillable =[
        'name',
        'genre_code',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function comics(){
        return $this->hasMany(Comic::class,'genre_id','id');
    }
}
