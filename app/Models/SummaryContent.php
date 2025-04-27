<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryContent extends BaseModel
{
    protected $table = "summary_contents";
    protected $fillable = [
        'content',
        'comic_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function comic()
    {
        return $this->belongsTo(Comic::class, 'comic_id');
    }
}
