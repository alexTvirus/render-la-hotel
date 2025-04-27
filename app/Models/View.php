<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends BaseModel
{
    protected $table = "views";
    protected $fillable =[

        'number_view',
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
