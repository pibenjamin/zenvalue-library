<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    /** @use HasFactory<\Database\Factories\DocFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'author_id',
    ];

    public function authors()
    {
        return $this->belongsToMany(User::class, 'doc_authors', 'doc_id', 'author_id');
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_docs', 'doc_id', 'training_id');
    }
    

    
}
