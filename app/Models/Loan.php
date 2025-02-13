<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    const STATUS_IN_PROGRESS        = 'in_progress';
    const STATUS_RETURNED           = 'returned';
    const STATUS_OVERDUE            = 'overdue';
    const STATUS_RETURN_IN_PROGRESS = 'return_in_progress';


    protected $fillable = [
        'book_id',
        'borrower_id',
        'borrowed_at',
        'returned_at',
        'to_be_returned_at',


        'return_signaled_at',
        'return_confirmed_by',
        'return_confirmation_token',
        'status'
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
        'to_be_returned_at' => 'datetime',
        'return_signaled_at' => 'datetime'
    ];

    private const STATUS_COLORS = [
        self::STATUS_IN_PROGRESS => 'primary',
        self::STATUS_RETURNED => 'success',
        self::STATUS_RETURN_IN_PROGRESS => 'warning',
        self::STATUS_OVERDUE => 'danger',
    ];

    private const STATUS_LABELS = [
        self::STATUS_IN_PROGRESS => 'En cours',
        self::STATUS_RETURNED => 'Retourné',
        self::STATUS_OVERDUE => 'En retard',
        self::STATUS_RETURN_IN_PROGRESS => 'Retour en cours',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
    
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_confirmed_by');
    }

    public function isOverdue(): bool
    {
        return $this->to_be_returned_at < now();
    }

    public function isReturned(): bool
    {
        return $this->returned_at !== null;
    }

    public function isInProgress(): bool
    {
        return $this->returned_at === null && !$this->isOverdue();
    }

    public function updateStatus(): void
    {
        if ($this->isOverdue()) {
            $this->status = self::STATUS_OVERDUE;
        } elseif ($this->isReturned()) {
            $this->status = self::STATUS_RETURNED;
        } else {
            $this->status = self::STATUS_IN_PROGRESS;
        }

        $this->save();  
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function getStatusColors(): array
    {
        return self::STATUS_COLORS;
    }

    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Inconnu';
    }
    
    public function getStatusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }

    



} 