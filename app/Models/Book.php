<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    const STATUS_TO_QUALIFY             = 'to_qualify';
    const STATUS_QUALIFIED              = 'qualified';
    const STATUS_REJECTED               = 'rejected';
    const STATUS_ON_SHELF               = 'on_shelf';
    const STATUS_BORROWED               = 'borrowed';
    const STATUS_MISSING                = 'missing';
    const STATUS_DROP_OFF               = 'drop_off';


    const LOCATION_OFFICE            = 'office';
    const LOCATION_KEEP_AT_HOME        = 'keep_at_home';

    const DIFFICULTY_LEVEL_EASY         = 'easy';
    const DIFFICULTY_LEVEL_MEDIUM       = 'medium';
    const DIFFICULTY_LEVEL_HARD         = 'hard';
    const DIFFICULTY_LEVEL_EXPERT       = 'expert';


    private const LOCATION_LABELS = [
        self::LOCATION_OFFICE               => 'Au bureau',
        self::LOCATION_KEEP_AT_HOME         => 'A la maison',
    ];

    private const STATUS_LABELS = [
        self::STATUS_TO_QUALIFY             => 'À qualifier',
        self::STATUS_QUALIFIED              => 'Qualifiée',
        self::STATUS_REJECTED               => 'Rejetée',
        self::STATUS_ON_SHELF               => 'Sur étagère',
        self::STATUS_BORROWED               => 'Emprunté',
        self::STATUS_MISSING                => 'Manquant',
        self::STATUS_DROP_OFF               => 'Déposé',
    ];

    private const LOCATION_COLORS = [
        self::LOCATION_OFFICE               => 'stone',
        self::LOCATION_KEEP_AT_HOME         => 'stone',
    ];

    private const STATUS_COLORS = [
        self::STATUS_TO_QUALIFY             => 'warning',
        self::STATUS_QUALIFIED              => 'stone',
        self::STATUS_REJECTED               => 'danger',
        self::STATUS_ON_SHELF               => 'success',
        self::STATUS_BORROWED               => 'danger',
        self::STATUS_MISSING                => 'danger',
        self::STATUS_DROP_OFF               => 'stone',
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
        'status',
        'location',
        'cal_page',
        'qr_code_interest'
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

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class, 'training_books');
    }

    public function putOnShelf()
    {
        $this->status = Book::STATUS_ON_SHELF;
        $this->save();
    }

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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
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

    public static function getLocations(): array
    {
        return self::LOCATION_LABELS;
    }

    public static function getLocationLabel(string|null $location): string
    {
        if($location === null) {
            $location = self::LOCATION_OFFICE;
        }
        return self::LOCATION_LABELS[$location] ?? 'Non défini';
    }

    public static function getLocationColors(): array
    {
        return self::LOCATION_COLORS;
    }

    public static function getLocationColor(string|null $location): string
    {
        if($location === null) {
            $location = self::LOCATION_OFFICE;
        }
        return self::LOCATION_COLORS[$location] ?? 'stone';
    }

    public function getStatusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Non défini';
    }

    public static function getStatusColors(): array
    {
        return self::STATUS_COLORS;
    }

    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
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
        return $this->loans
                ->whereIn('status',['in_progress', 'returned_in_progress'])->count();
    }

    public function isBorrowed()
    {
        return $this->loans
                ->whereIn('status',['in_progress', 'returned_in_progress'])->count();
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