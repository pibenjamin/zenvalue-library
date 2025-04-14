<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /** @use HasFactory<\Database\Factories\LinkFactory> */
    use HasFactory;

    protected $fillable = [
        'url',
        'name',
    ];

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_links', 'link_id', 'training_id');
    }
    

}
