<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'hero_image',
    ];

    protected $appends = ['hero_image_url'];

    public function getHeroImageUrlAttribute()
    {
        return $this->hero_image
            ? \Illuminate\Support\Facades\Storage::disk('public_direct')->url($this->hero_image)
            : null;
    }
}

