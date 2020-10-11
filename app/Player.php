<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_group',
        'display_name'
    ];

    /**
     * Get the phone record associated with the user.
     */
    public function group()
    {
        return $this->belongsTo('App\Group');
    }
}
