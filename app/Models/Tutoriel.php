<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Tutoriel extends Model implements Sortable
    {
    /** @use HasFactory<\Database\Factories\TutorielFactory> */
    use HasFactory, SortableTrait;

    protected $fillable = [
        'titre',
        'description',
        'video_url',
        'order',
        'is_active',
    ];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];
}
