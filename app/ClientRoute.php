<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientRoute extends Model
{
    protected $table = 'client_route';
    protected $guarded = array();
    public $timestamps = false;

    //This function generates query builder where are selected only clientRoutes with status 1
    public function scopeOnlyActiveClientRoutes($query) {
        $query->where('client_route.status', '=', 1);
    }
}
