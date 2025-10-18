<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Work extends Model
{
    use HasFactory;

    protected $table = 'works';

    protected $fillable = [
        'title',
        'image',
    ];

    // որ JSON-ում էլ ավտոմատ հայտնվի
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('public_direct')->url($this->image)
            : null;
    }
}
