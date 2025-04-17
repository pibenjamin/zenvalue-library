<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcours extends Model
{
    /** @use HasFactory<\Database\Factories\ParcoursFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    const STATUS_ONLINE               = 'online';
    const STATUS_OFFLINE              = 'offline';

    private const STATUS_LABELS = [
        self::STATUS_ONLINE => 'En ligne',
        self::STATUS_OFFLINE => 'Hors ligne',
    ];

    private const STATUS_COLORS = [
        self::STATUS_ONLINE => 'success',
        self::STATUS_OFFLINE => 'danger',
    ];

    public static function getStatusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public static function getStatusColors(): array
    {
        return self::STATUS_COLORS;
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUS_LABELS[$this->status];
    }

    public function getStatusColorAttribute()
    {
        return self::STATUS_COLORS[$this->status];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'parcours_users', 'parcours_id', 'user_id');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'parcours_books', 'parcours_id', 'book_id');
    }
}
