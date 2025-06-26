<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     * 
     */
    protected $fillable = [
        'image',
        'title',
        'description',
        'price',
        'stock',
    ];

    /**
     * image
     * 
     * @return Attribute
     */

     protected function image() : Attribute
     {
        return Attribute::make(
            get: fn ($image) => url('/storage/products/' . $image),
        );
     }
}
