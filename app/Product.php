<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function baskets(){
        return $this->hasMany('App\Basket');
    }
}
