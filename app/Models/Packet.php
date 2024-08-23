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

class Packet extends BaseModel
{

    protected $table = "packets";
    protected $fillable = [
        'base_price',
        'name_packet',

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



    public function roomTypePackets()
    {
        return $this->hasMany(RoomTypePacket::class, 'packet_id');
    }

    public function packetBenefits()
    {
        return $this->hasMany(PacketBenefit::class, 'packet_id');
    }


    public function benefits()
    {
        return $this->belongsToMany(Benefit::class, 'packet_benefit', 'packet_id', 'benefit_id');
    }
}
