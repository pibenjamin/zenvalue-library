<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{

    const STATUS_CONTRIBUTION_TO_QUALIFY          = 'contribution_to_qualify';
    const STATUS_CONTRIBUTION_QUALIFIED           = 'contribution_qualified';
    const STATUS_CONTRIBUTION_REJECTED            = 'contribution_rejected';
    const STATUS_ON_SHELF                         = 'on_shelf';
    const STATUS_BORROWED                         = 'borrowed';
    const STATUS_MISSING                          = 'missing';

    const DIFFICULTY_LEVEL_EASY         = 'easy';
    const DIFFICULTY_LEVEL_MEDIUM       = 'medium';
    const DIFFICULTY_LEVEL_HARD         = 'hard';
    const DIFFICULTY_LEVEL_EXPERT       = 'expert';

    private const STATUS_LABELS = [
        self::STATUS_CONTRIBUTION_TO_QUALIFY => 'Contribution à qualifier',
        self::STATUS_CONTRIBUTION_QUALIFIED  => 'Contribution qualifiée',
        self::STATUS_CONTRIBUTION_REJECTED   => 'Contribution rejetée',
        self::STATUS_ON_SHELF                => 'Sur étagère',
        self::STATUS_BORROWED                => 'Emprunté',
        self::STATUS_MISSING                 => 'Manquant',
    ];

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
        'description',
        'missing',
        'difficulty_level',
        'amazon_content_page',
        'tags',
        'ol_key',
        'lang',
        'status'
    ];
    protected $casts = [
        'is_borrowed' => 'boolean',
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

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
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

    public function getLastLoan()
    {
        return $this->loans()->latest()->first();
    }

    public static function getDifficulties(): array
    {
        return self::DIFFICULTY_LABELS;
    }

    public static function getStatusLabels(): array
    {
        return self::STATUS_LABELS;
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

    public function getAverageRating(): float
    {

        if($this->ratings()->count() === 0) {
            return 0;
        }

        return round($this->ratings()->avg('rate'), 1);
    }

    public function getAverageRoundedRating(): int
    {
        return round($this->getAverageRating());
    }

    public function getUserRating(User $user): ?int
    {
        if($this->ratings()->where('user_id', $user->id)->exists()) {
            return $this->ratings()->where('user_id', $user->id)->first()->rate;
        }
        return null;
    }

    public function hasBeenLoanedToUser(User $user): bool
    {
        return $this->loans()->where('user_id', $user->id)->exists();
    }

    public function addTags(array $tags): void
    {
        $this->tags()->attach($tags);
    }

} 