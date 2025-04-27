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

class Comic extends BaseModel
{
    use SearchableTraitExtend;
    use HasSlug;
    protected $table = "comics";
    protected $fillable =[
        'comic_code',
        'comic_name',
        'bg_color',
        'link_banner',
        'link_banner_backup',
        'link_video_banner',
        'link_video_banner_2',
        'ggdrive_id',
        'link_bg',
        'link_bg_backup',
        'link_avatar',
        'link_avatar_backup',
        'link_comic_name',
        'link_comic_name_backup',
        'link_comic_small_name',
        'link_comic_small_name_backup',
        'tranfer_color',
        'total_view',
        'total_like',
        'slug',

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

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'comics.comic_name'=> 10
        ]
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->load('chapters','taggeds');
            $chapterServices = app()->make(ChapterServices::class);
            $taggedsServices = app()->make(TaggedServices::class);
            if(isset($model->chapters)){
                foreach ($model->chapters as $chapter){
                    $chapterServices->delete($chapter->id);
                }
            }
            if(isset($model->taggeds)){
                foreach ($model->taggeds as $tagged){
                    $taggedsServices->delete($tagged->id);
                }
            }

            $model->summaryContents()->delete();
            return true;
        });
    }

    public function getSlugOptions() :  SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('comic_name')
            ->saveSlugsTo('slug');
    }

    public function chapters(){
        return $this->hasMany(Chapter::class,'comic_id');
    }

    public function taggeds(){
        return $this->hasMany(Tagged::class,'comic_id');
    }

    public function hashtags(){
        return $this->belongsToMany(Hashtag::class,'taggeds','comic_id','hashtag_id');
    }

    public function summaryContents(){
        return $this->hasOne(SummaryContent::class,'comic_id');
    }

    public function artist(){
        return $this->belongsTo(Artist::class,'artist_id');
    }

    public function author(){
        return $this->belongsTo(Author::class,'author_id');
    }

    public function publisher(){
        return $this->belongsTo(Author::class,'publisher_id');
    }

    public function genre(){
        return $this->belongsTo(Author::class,'genre_id');
    }
}
