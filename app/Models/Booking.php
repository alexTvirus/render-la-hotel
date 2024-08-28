<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Traits\SearchableTraitExtend;

class Booking extends BaseModel
{

    protected $table = "bookings";
    protected $fillable =[
        'checkin_at',
        'checkout_at',
        'total_price',
        'number_guests',
        'status',
        'customer_id',

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

    public function roomBookings(){
        return $this->hasMany(RoomBooking::class,'booking_id');
    }

    public function bookingStatus(){
        return $this->belongsTo(BookingStatus::class,'status');
    }

    public function customer(){
        return $this->belongsTo(User::class,'customer_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class,'booking_id');
    }


    public function rooms(){
        return $this->belongsToMany(Rooms::class,'room_booking','booking_id','room_id');
    }

}
