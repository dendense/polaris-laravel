<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'photo', 'location', 'place_name', 'description',
        'transportation', 'demography', 'user_id'
    ];

     /**
     * Get the post its owner
     */
    public function user()
    {
        return $this->belongsTo('App\user');
    }
}
