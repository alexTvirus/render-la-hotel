<?php

namespace App\Models;

use App\Services\ChapterServices;
use App\Services\HashtagServices;
use App\Services\RoomTypePacketServices;
use App\Services\TaggedServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Traits\SearchableTraitExtend;

class Rating extends BaseModel
{

    protected $table = "ratings";
    protected $fillable =[
        'comment',
        'rate',
        'customer_id',
        'room_type_packet_id',


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
        //todo: nếu có lệnh create >> cập nhật rate của bảng room type packet
        static::created(function ($model) {
            $roomTypePacketServices = app()->make(RoomTypePacketServices::class);
            $room_type_packet = $roomTypePacketServices->show($model->room_type_packet_id);
            if($room_type_packet){
                $ratings = $room_type_packet->ratings;
                $room_type_packet->rate = $ratings->avg('rate');
                $roomTypePacketServices->save($room_type_packet->toArray());
            }
        });
    }

//    public function getSlugOptions() :  SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom('comic_name')
//            ->saveSlugsTo('slug');
//    }

}
