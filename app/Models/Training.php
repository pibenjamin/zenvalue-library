<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Training extends Model
{
    protected $fillable = [
        'title',
        'description',
        'url',
        'image',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'training_books');
    }

} 