<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort',
        'active',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
