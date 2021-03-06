<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];


    /**
     * Many to many relationship with App\User model
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     *  Check that group has user
     *
     *  @param int $userId
     *  @return bool
     */
    public function hasUser(int $userId)
    {
        return $this->users()->whereUserId($userId)->exists();
    }
}
