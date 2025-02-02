<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
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
} 