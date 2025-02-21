<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{

    const DIFFICULTY_LEVEL_EASY         = 'easy';
    const DIFFICULTY_LEVEL_MEDIUM       = 'medium';
    const DIFFICULTY_LEVEL_HARD         = 'hard';
    const DIFFICULTY_LEVEL_EXPERT       = 'expert';

    private const DIFFICULTY_LABELS = [
        self::DIFFICULTY_LEVEL_EASY     => 'Facile',
        self::DIFFICULTY_LEVEL_MEDIUM   => 'Moyen',
        self::DIFFICULTY_LEVEL_HARD     => 'Difficile',
        self::DIFFICULTY_LEVEL_EXPERT   => 'Expert',
    ];  

    private const DIFFICULTY_COLORS = [
        self::DIFFICULTY_LEVEL_EASY     => 'success',
        self::DIFFICULTY_LEVEL_MEDIUM   => 'warning',
        self::DIFFICULTY_LEVEL_HARD     => 'danger',
        self::DIFFICULTY_LEVEL_EXPERT   => 'expert',
    ];

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
        'year_of_publication',
        'publisher',
        'quantity',
        'support_id',
        'is_borrowed',
        'missing',
        'difficulty_level',
        'amazon_content_page'
    ];
    protected $casts = [
        'is_borrowed' => 'boolean',
        'open_library_parsed' => 'boolean',
        'year_of_publication' => 'integer',
        'quantity' => 'integer',
        'pages' => 'integer',
        'missing' => 'boolean',
        'difficulty_level' => 'string',
        'amazon_content_page' => 'string'
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

    public function getLastLoan(): Loan
    {
        return $this->loans()->latest()->first();
    }

    public static function getDifficulties(): array
    {
        return self::DIFFICULTY_LABELS;
    }

    public function getDifficultyLabel(): string
    {
        return self::DIFFICULTY_LABELS[$this->difficulty_level] ?? 'Non défini';
    }

    public function getDifficultyColor(): string
    {
        return self::DIFFICULTY_COLORS[$this->difficulty_level] ?? 'secondary';
    }

    public function isBorrowedByUser(User $user): int
    {


        return $this->loans->whereIn('status',['in_progress', 'returned_in_progress'])->count();
    }

} 