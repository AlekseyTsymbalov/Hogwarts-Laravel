<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'section_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
