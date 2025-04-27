<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends BaseModel
{
    const LIMIT_PAGE = 10;
    protected $table = "content_images";
    protected $fillable =[

        'link_img',
        'link_img_backup',
        'chapter_id',
        'ggdrive_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
    public function chapter(){
        return $this->belongsTo(Chapter::class,'chapter_id');
    }
}
