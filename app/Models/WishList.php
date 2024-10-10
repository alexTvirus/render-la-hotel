<?php

namespace App\Models;

use App\Services\ChapterServices;
use App\Services\HashtagServices;
use App\Services\TaggedServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Traits\SearchableTraitExtend;

class WishList extends BaseModel
{

    protected $table = "wishlists";
    protected $fillable =[
        'customer_id',
        'room_type_id',


        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    const TIME = [
        'application_date',
        'attendance_start_at',
        'attendance_end_at',
        'approved_at'
    ];


    public static function boot()
    {
        parent::boot();

    }

//    public function getSlugOptions() :  SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom('comic_name')
//            ->saveSlugsTo('slug');
//    }
    public function user(){
        return $this->belongsTo(User::class,'customer_id');
    }

    public function roomType(){
        return $this->belongsTo(RoomType::class,'room_type_id');
    }
}
