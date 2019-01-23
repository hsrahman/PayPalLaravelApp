<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayPalTransaction extends Model
{
    protected $table = 'paypal_transaction';

    public function user(){
        return $this->belongsTo('App\User');
    }

}
