<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends BaseModel
{
    protected $table = "likes";
    protected $fillable =[

        'number_like',
        'chapter_id',

        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
    public function chapter(){
        return $this->belongsTo(Chapter::class,'chapter_id');
    }
}
