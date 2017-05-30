<?php

namespace App;

use App\Discussion;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /**
     * The table associated with the model
     * 
     * @var string
     */
    protected $table = 'channels';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_restricted',
        'is_hidden',
    ];

    /**
     * Get the discussions for the channel
     */
    public function discussions()
    {
    	return $this->hasMany(Discussion::class);
    }
}
