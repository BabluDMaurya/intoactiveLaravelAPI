<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
     public function states()
    {
        return $this->hasMany('App\Model\State');
    }

    public function cities()
    { 
        return $this->states()->with('App\Model\City');
    }
}
