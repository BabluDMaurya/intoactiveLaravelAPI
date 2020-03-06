<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
   protected $fillable = [
         'following_uid','followers_uid'
     ];
   
    public function followingUser()
    {
        return $this->belongsTo('App\Model\User', 'following_uid');
    }
    public function followerUser()
    {
        
        return $this->belongsTo('App\Model\User', 'followers_uid');
    }

    
   
}
