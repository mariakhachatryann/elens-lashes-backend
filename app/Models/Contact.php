<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'phone',
        'email',
        'social_links',
        'logo',
        'book_link',
        'map_url',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];
}
