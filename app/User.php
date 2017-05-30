<?php

namespace App;

use App\Comment;
use App\Discussion;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Get the user avatar
     * @return string
     */
    public function getAvatarAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->comment_author_email) . '?s=45&d=mm';
    }
}
