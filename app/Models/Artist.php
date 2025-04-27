<?php

namespace App\Models;


class Artist extends BaseModel
{
    protected $table = "artists";
    protected $fillable =[
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function comics(){
        return $this->hasMany(Comic::class,'artist_id','id');
    }
}
