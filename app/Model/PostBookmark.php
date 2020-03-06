<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostBookmark extends Model
{
    //
     protected $fillable = ['user_id','post_id', 'is_unliked'];
    
      public function posts()
    {
        return $this->belongsTo('App\Model\Posts', 'post_id');
    }
}
