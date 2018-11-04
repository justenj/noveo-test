<?php

namespace App;

use App\Exceptions\UndefinedUserStateException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const NON_ACTIVE_STATE = 'non active';
    const ACTIVE_STATE = 'active';

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'last_name', 'first_name', 'state'
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public static function states()
    {
        return [
            'NON_ACTIVE_STATE' => static::NON_ACTIVE_STATE,
            'ACTIVE_STATE' => static::ACTIVE_STATE
        ];
    }

    public function setStateAttribute($state)
    {
        if (!in_array($state, static::states())) {
            throw new UndefinedUserStateException("Undefined '$state' state");
        }

        $this->attributes['state'] = $state;
    }
}
