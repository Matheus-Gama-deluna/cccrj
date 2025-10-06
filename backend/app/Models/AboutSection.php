<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'section_type',
        'order',
        'is_active',
        'image_url',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];
}