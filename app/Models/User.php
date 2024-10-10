<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        "last_name",
        "phone",
		"national",
		"isActive",
		"birthday",
    ];

    protected $table = "users";

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
		'phone_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    function bookings(){
        return $this->hasMany(Booking::class,'customer_id');
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'wishlists', 'customer_id', 'room_type_id')->withTimestamps();
    }

    function wishlists(){
        return $this->hasMany(WishList::class,'customer_id')->orderBy('updated_at', 'desc');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('isActive', 1);
    }

    public function scopeVerified(Builder $query): void
    {
        $query->whereNotNull('email_verified_at');
    }
}
