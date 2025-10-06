<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'date',
        'document_type',
        'image_url',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'date',
    ];
}