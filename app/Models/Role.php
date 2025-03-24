<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name'
    ];

//    public function users(): BelongsToMany
//    {
//        return $this->belongsToMany(User::class, 'user_roles');
//    }


    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id');
    }




}
