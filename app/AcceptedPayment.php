<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcceptedPayment extends Model
{
    protected $table = 'accepted_payments';

    public function cadre() {
        return $this->belongsTo('App\User', 'cadre_id');
    }
    public function accepted_payment_user_story() {
        return $this->hasMany('App\AcceptedPaymentUserStory', 'accepted_payment_id');
    }
}
