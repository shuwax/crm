<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTypes extends Model
{
    protected $table = 'user_types';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name','all_departments',
    ];

    public function users() {
        return $this->hasMany('App\User', 'user_type_id');
    }

    public function employee_of_the_week() {
        return $this->hasMany('App\EmployeeOfTheWeek', 'user_type_id');
    }

    public function accepted_payment_user_story() {
        return $this->hasMany('App\AcceptedPaymentUserStory', 'user_type_id');
    }
}
