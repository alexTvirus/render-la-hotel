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

class RoomType extends BaseModel
{

	protected $hidden = ['pivot','laravel_through_key'];
    protected $table = "room_types";
    protected $fillable =[
        'name',
        'description',
        'base_price',
        'max_occupancy',
        'room_size',
        'bathrooms',
        'sleeps',

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
            $roomTypePackets = $model->roomTypePackets;
            $rooms= $model->rooms;
            $ratings= $model->ratings;

            if(!$roomTypePackets->isEmpty() ||
                !$rooms->isEmpty() ||
                !$ratings->isEmpty() ){
                return false;
            }
            $model->roomTypeImages()->delete();
        });
    }

//    public function getSlugOptions() :  SlugOptions
//    {
//        return SlugOptions::create()
//            ->generateSlugsFrom('comic_name')
//            ->saveSlugsTo('slug');
//    }

    public function roomTypePackets(){
        return $this->hasMany(RoomTypePacket::class,'room_type_id');
    }

    public function rooms(){
        return $this->hasManyThrough(Room::class,RoomTypePacket::class);
    }

    public function ratings(){
        return $this->hasManyThrough(Rating::class,RoomTypePacket::class);
    }
	
	public function amenityRoomTypes(){
        return $this->hasMany(AmenityRoomType::class,'room_type_id');
    }

    public function amenities(){
        return $this->belongsToMany(Amenity::class,'amenity_room_type','room_type_id','amenity_id');
    }

    public function packets(){
        return $this->belongsToMany(Packet::class,'room_type_packet','room_type_id','packet_id');
    }

    public function imageTypes(){
        return $this->belongsToMany(ImageType::class,'image_types','room_type_id','image_type_id');
    }

    public function roomTypeImages(){
        return $this->hasMany(RoomTypeImage::class,'room_type_id');
    }

    function wishlists(){
        return $this->hasMany(WishList::class,'room_type_id')->withTimestamps();
    }
	
	 public function users()
    {
        return $this->belongsToMany(User::class, 'wishlists','room_type_id', 'customer_id' )->withTimestamps();
    }


    public function scopeHiddenProperty(){
        $this->makeHidden(['created_by', 'updated_by', 'created_at', 'updated_at']);
    }
}
