<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcceptedPaymentUserStory extends Model
{
    protected $table = 'accepted_payment_user_story';

    public function accepted_payments() {
        return $this->belongsTo('App\AcceptedPayment', 'accepted_payment_id');
    }
    public function department_info() {
        return $this->belongsTo('App\Department_info', 'department_info_id');
    }
    public function agency() {
        return $this->belongsTo('App\Agencies', 'agency_id');
    }
    public function user_type() {
        return $this->belongsTo('App\UserTypes', 'user_type_id');
    }
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
