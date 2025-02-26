<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    protected $fillable = [
        'name',
        'photo_url',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_books');
    }

    public function aquisitionRequests(): HasMany
    {
        return $this->hasMany(AquisitionRequest::class);
    }
} 