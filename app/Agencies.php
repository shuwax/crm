<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agencies extends Model
{
    protected $table = 'agencies';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name',
    ];

    public function users() {
        return $this->hasMany('App\User', 'agency_id');
    }

    public function accepted_payment_user_story() {
        return $this->hasMany('App\AcceptedPaymentUserStory', 'agency_id');
    }
}
