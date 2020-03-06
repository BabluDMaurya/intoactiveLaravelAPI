<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PostComments extends Model
{
    protected $fillable = [
         'post_id','user_id','comment','reply_to','comment_delete'
     ];
     
     public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    
    public function  commentReply(){
        return $this->hasMany('App\Model\PostComments' , 'reply_to')->where('comment_delete','0');
    }
}
