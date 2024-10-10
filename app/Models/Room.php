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

class Room extends BaseModel
{
    protected $hidden = ['pivot','laravel_through_key'];
    protected $table = "rooms";
    protected $fillable =[
        'room_view',
        'room_number',
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
        static::deleting(function ($model) {
            $roomBookings = $model->roomBookings;
            $roomTypePacket= $model->roomTypePacket;

            if(!$roomBookings->isEmpty() ||
                !empty($roomTypePacket->start_at) ||
                !empty($roomTypePacket->end_at)){
                return false;
            }
        });
    }

//    public function getSlugOptions() :  SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom('comic_name')
//            ->saveSlugsTo('slug');
//    }

    public function roomBookings(){
        return $this->hasMany(RoomBooking::class,'room_id');
    }

    public function roomTypePacket(){
        return $this->belongsTo(RoomTypePacket::class,'room_type_packet_id');
    }


    public function bookings(){
        return $this->belongsToMany(Booking::class,'room_booking','room_id','booking_id');
    }
}
