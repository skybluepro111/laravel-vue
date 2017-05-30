<?php

namespace App;

use App\Channel;
use App\Comment;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected $table = 'discussions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'channel_id',
        'title',
        'slug',
        'body',
        'is_approved',
        'is_locked',
        'is_sticky',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s",$value)->diffForHumans();
        return $date;
    }
}
