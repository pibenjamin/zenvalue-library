<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AquisitionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'status',
        'author',
        'title',
        'isbn',
        'image',
        'link_to_book',
        'price',
        'validated_at',
    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}   
