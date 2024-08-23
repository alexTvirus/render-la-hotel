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

class RoomTypePacket extends BaseModel
{

    protected $table = "room_type_packet";
    protected $fillable =[
        'room_type_id',
        'packet_id',


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
    public function roomType(){
        return $this->belongsTo(RoomType::class,'room_type_id');
    }

    public function rooms(){
        return $this->hasMany(Rooms::class,'room_type_packet_id');
    }

    public function packet(){
        return $this->belongsTo(Packet::class,'packet_id');
    }

    public function ratings(){
        return $this->hasMany(Rating::class,'room_type_packet_id');
    }
}
