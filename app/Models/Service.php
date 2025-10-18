<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'title',
        'description',
        'price',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'parent_id' => 'integer',
    ];

    // որ JSON-ում էլ ավտոմատ հայտնվի
    protected $appends = ['image_url'];

    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::disk('public_direct')->url($this->image)
            : null;
    }
}
