<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'parent_id',
        'is_last_level',
        'old_slug',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function products()
    {
        return $this->belongsToMany('App\Models\Product')
            ->withTimestamps();
    }


    public function scopeWithProduct($query, $productId)
    {
        $query->whereHas('products', function ($q) use ($productId) {
            $q->where('product_id', $productId);
        });
    }
}
