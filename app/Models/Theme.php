<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug'
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
} 