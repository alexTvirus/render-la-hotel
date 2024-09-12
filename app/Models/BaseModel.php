<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    use HasFactory;
    const LIMIT_PAGE = 10;
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (Auth::user()) {
                if (!$model->created_by && Schema::hasColumn($model->getTable(), 'created_by')) {
                    $model->created_by = Auth::user()->id;
                }

                if (!$model->updated_by && Schema::hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = Auth::user()->id;
                }
            }
        });

        self::saving(function ($model) {
            if (Auth::user() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::user()->id;
            }
        });
    }
}
