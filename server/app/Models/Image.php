<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Ramsey\Uuid\Uuid;

class Image extends Model
{
    public static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            File::delete(
                Config::get("app.images.uploadDirectory") . "/" . $model->origin,
                Config::get("app.images.uploadDirectory") . "/" . $model->name,
                Config::get("app.images.uploadDirectory") . "/" . $model->thumbnail_name
            );
        });

        static::creating(function ($model) {
            $model->origin = $model->name;

            $img = \Intervention\Image\Facades\Image::make(Config::get("app.images.uploadDirectory") . "/" . $model->origin);
            $filename = Uuid::uuid4()->toString() . "." . strtolower($img->extension);
            $img->resize(1000, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })
                ->save($img->dirname . "/" . $filename, 80)
                ->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($img->dirname . "/" . "thumb-" . $filename, 80);

            $model->name = $filename;
            $model->thumbnail_name = "thumb-" . $filename;

            return true;
        });
    }

    protected $fillable = [
        'name',
        'thumbnail_name',
        'product_id',
        'origin'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function product(): HasOne
    {
        return $this->hasOne('App\Models\Product');
    }
}
