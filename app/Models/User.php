<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;

use Filament\Models\Contracts\HasAvatar;


class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_activated',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_SUPER_ADMIN  = 1;
    const ROLE_ADMIN        = 2;
    const ROLE_USER         = 3;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        //return str_ends_with($this->email, '@gmail.com');
        return $this->hasRole('super_admin') || $this->hasRole('admin') || $this->hasRole('user');
        //return $this->hasRole('admin'
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    public function canBorrow(): bool
    {
        $currentLoans = $this->loans()->where('returned_at', null)->count();
        $maxLoans = config('app.max_loans');
        
        return $currentLoans < $maxLoans;
    }

    public function canManageSettings(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    public function isLibrarian(): bool
    {
        return $this->role->name === 'librarian';
    }   

    public function isUser(): bool
    {
        return $this->role->name === 'user';
    }
    

}
