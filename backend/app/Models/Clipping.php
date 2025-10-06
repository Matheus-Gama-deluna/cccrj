<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'summary',
        'content',
        'source_url',
        'date',
        'category',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'date',
    ];
}