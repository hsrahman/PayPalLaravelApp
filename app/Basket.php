<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{	

	 protected $table = 'basket';
	 public $timestamps = false;

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function products(){
        return $this->hasMany('App\Product');
    }
}
