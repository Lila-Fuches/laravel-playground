<?php

namespace Domain\User\Models;

use Domain\User\Collections\UserCollection;
use Support\HasUuid;
use Domain\User\Builders\UserBuilder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasUuid;
    use Notifiable;
    use SoftDeletes;

    protected array $fillable = [
        'uuid',
       'first_name',
        'last_name',
        'email',
        'password',
        'verification_token'
    ];

    protected array $hidden = [
        'password', 'remember_token',
    ];

    protected array $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function query(): UserBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    public function newCollection(array $models = []): UserCollection
    {
        return new UserCollection($models);
    }

    /**
     * Relations
     */


    /**
     * Other stuff
     */
}
