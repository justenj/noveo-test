<?php

namespace App;

use App\Exceptions\UndefinedUserStateException;
use Illuminate\Notifications\Notifiable;
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

    /**
     * Many to many relationship with App\Group model
     */
    public function groups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * Returns available states
     */
    public static function states(): array
    {
        return [
            'NON_ACTIVE_STATE' => static::NON_ACTIVE_STATE,
            'ACTIVE_STATE' => static::ACTIVE_STATE
        ];
    }

    /**
     * Checks that state is valid and if it's, set new state
     *
     * @param string $state
     * @throws UndefinedUserStateException
     */
    public function setStateAttribute(string $state): void
    {
        if (!$this->isValidState($state)) {
            throw new UndefinedUserStateException("Undefined '$state' state");
        }

        $this->attributes['state'] = $state;
    }

    /**
     * Check that state is valid
     *
     * @param string $state
     * @return bool
     */
    private function isValidState(string $state): bool
    {
        return in_array($state, static::states());
    }
}
