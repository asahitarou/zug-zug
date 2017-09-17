<?php

declare(strict_types = 1);

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Product extends Model
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $images = $model->pictures;

            foreach ($images as $image) {
                File::delete(
                    Config::get("app.images.uploadDirectory") . "/" . $image->origin,
                    Config::get("app.images.uploadDirectory") . "/" . $image->name,
                    Config::get("app.images.uploadDirectory") . "/" . $image->thumbnail_name
                );
            }
        });
    }

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'attributes',
        'is_active',
        'price',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function pictures(): HasMany
    {
        return $this->hasMany('App\Models\Image'); //  or whatever your namespace is
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category')
            ->withTimestamps();
    }
}
