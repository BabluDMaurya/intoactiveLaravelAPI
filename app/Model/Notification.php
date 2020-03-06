<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon;
class Notification extends Model
{
    protected $fillable = ['posts_id', 'user_id', 'following_uid', 'is_read','type','description'];
    /**
    * Get the user
    */
    public function user()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }
    public function getTypeAttribute($value)
    {        
        if($value === 1){
            return config('constants.NOTIFICATION_POST_TITLE');
        }
        else if($value === 2){
            return config('constants.NOTIFICATION_VIDEO_TITLE');
        }
        else if($value === 3){
            return config('constants.NOTIFICATION_PROGRAM_TITLE');
        }
        else if($value === 4){
            return config('constants.NOTIFICATION_FOLLOWER_TITLE');
        }
        else if($value === 5){
            return config('constants.NOTIFICATION_CHAT_TITLE');
        }
        else if($value === 8){
            return config('constants.NOTIFICATION_POST_TITLE');
        }
        else if($value === 9){
            return config('constants.NOTIFICATION_COMMENT_TITLE');
        }
    }
    public function getDescriptionAttribute($value)
    {        
        return ucfirst($value);
    }
    public function getCreatedAtAttribute($value) {
        return \Carbon\Carbon::parse($value)->diffForhumans();
    }    
}
