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

    public function docs(): BelongsToMany
    {
        return $this->belongsToMany(Doc::class, 'training_docs');
    }

    public function links(): BelongsToMany
    {
        return $this->belongsToMany(Link::class, 'training_links');
    }

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_trainers', 'training_id', 'trainer_id');
    }
    

} 