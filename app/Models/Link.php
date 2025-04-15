<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Link extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\LinkFactory> */
    use HasFactory, SortableTrait;

    protected $fillable = [
        'url',
        'name',
        'order',
    ];


    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_links', 'link_id', 'training_id');
    }
    

}
