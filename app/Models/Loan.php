<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    const STATUS_IN_PROGRESS    = 'in_progress';
    const STATUS_RETURNED       = 'returned';
    const STATUS_OVERDUE        = 'overdue';

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