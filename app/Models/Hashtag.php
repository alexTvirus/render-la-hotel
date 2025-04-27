<?php

namespace App\Models;

use App\Services\TaggedServices;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Hashtag extends BaseModel
{
    use HasSlug;
    protected $table = "hashtags";
    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->load('tagges');
            $taggedsServices = app()->make(TaggedServices::class);
            if(isset($model->tagges)){
                foreach ($model->tagges as $tagged){
                    $taggedsServices->delete($tagged->id);
                }
            }
            return true;
        });
    }

    public function comics(){
        return $this->belongsToMany(Comic::class,'taggeds','hashtag_id','comic_id');
    }
    public function tagges()
    {
        return $this->hasMany(Tagged::class, 'hashtag_id');
    }

    public function getSlugOptions() :  SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

}
