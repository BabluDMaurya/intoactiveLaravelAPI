<?php

namespace App\Model;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Http\Traits\UserTimezoneAware;

class Posts extends Model
{
    //
    protected $fillable = ['user_id','post_type', 'disable_comment', 'is_delete'];
       
     public function imagePost()
    {
        return $this->hasMany('App\Model\ImagePosts', 'post_id');
    }
    public function postLikes()
    {
        return $this->hasMany('App\Model\PostLikes', 'post_id')->where('is_unliked','0');
    }
    public function postBookmarks()
    {
        return $this->hasMany('App\Model\PostBookmark', 'post_id')->where('is_delete','0');
    }
     public function  postComments()
    {
        return $this->hasMany('App\Model\PostComments' , 'post_id')->where('comment_delete','0')->where('reply_to',null);
    }
     public function postUser()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
     public function  totalComments()
    {
        return $this->hasMany('App\Model\PostComments' , 'post_id')->where('comment_delete','0');
    }
    
    
    public function getCreatedAtAttribute($value) {
        return \Carbon\Carbon::parse($value)->diffForhumans();
    }   
}
