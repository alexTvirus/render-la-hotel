<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Traits\SearchableTraitExtend;

class Booking extends BaseModel
{

	protected $hidden = ['pivot','laravel_through_key'];
    protected $table = "bookings";
    protected $fillable =[
        'checkin_at',
        'checkout_at',
        'total_price',
        'number_guests',
        'status',
        'customer_id',
		'cancel_reason',

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

    public function scopeStatus(Builder $query,$status): void
    {
        $query->where('status',$status);
    }

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
        return $this->belongsToMany(Room::class,'room_booking','booking_id','room_id');
    }

}
