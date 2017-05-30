<?php

namespace App;

use App\Discussion;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected $table = 'comments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'discussion_id',
        'title',
        'content',
        'approved',
    ];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s",$value)->diffForHumans();
        return $date;
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
