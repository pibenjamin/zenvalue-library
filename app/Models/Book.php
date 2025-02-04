<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author',
        'cover_url',
        'google_api_page',
        'isbn',
        'is_borrowed',
        'open_library_parsed',
        'original_filename',
        'owner_id',
        'pages',
        'published_at',
        'publisher',
        'quantity',
        'support_id',
        'theme_id',
    ];

    protected $casts = [
        'is_borrowed' => 'boolean',
        'open_library_parsed' => 'boolean',
        'published_at' => 'date',
        'quantity' => 'integer',
        'pages' => 'integer',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_books');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_books');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function support(): BelongsTo
    {
        return $this->belongsTo(Support::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
} 