<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AquisitionRequest extends Model
{

    const STATUS_PENDING            = 'pending';
    const STATUS_REJECTED           = 'rejected';
    const STATUS_VALIDATED          = 'validated';
    const STATUS_PENDING_AQUISITION = 'pending_aquisition';


    private const STATUS_LABELS = [
        self::STATUS_PENDING => 'En attente',
        self::STATUS_REJECTED => 'Rejetée',
        self::STATUS_VALIDATED => 'Validée',
        self::STATUS_PENDING_AQUISITION => 'Acquisition en cours',
    ];

    private const STATUS_COLORS = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_VALIDATED => 'success',
        self::STATUS_PENDING_AQUISITION => 'info',
    ];


    protected $fillable = [
        'user_id',
        'description',
        'status',
        'author_id',
        'title',
        'isbn',
        'image',
        'link_to_book',
        'price',
        'validated_at',
        'reject_reason',

    ];  

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }



    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Inconnu';
    }
    
    public function getStatusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public static function getStatusLabelsForAdmin(): array
    {
        return self::STATUS_LABELS;
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'gray';
    }

    public function getStatusColors(): array
    {
        return self::STATUS_COLORS;
    }

    public static function getStatusColorsForAdmin(): array
    {
        return self::STATUS_COLORS;
    }   

}   
