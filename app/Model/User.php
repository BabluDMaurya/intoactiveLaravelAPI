<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Auth;
use Carbon\Carbon;
use App\Http\Traits\UserTimezoneAware;

class User extends Authenticatable
{
	use Notifiable, HasApiTokens,UserTimezoneAware;
    //
    protected $table ="users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'email', 'password', 'gender','otp_code', 'is_verified', 'location' , 'ip_address', 'is_delete','timezone','user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];    
    
    public function AauthAcessToken(){
        return $this->hasMany('\App\model\OauthAccessToken','user_id');
    }
     /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('user_name', $username)->first();
    }
    
    public function bios()
    {
        return $this->hasOne('App\Model\Bio', 'user_id');
    }
     public function additional()
    {
        return $this->hasOne('App\Model\AdditionalInfo', 'user_id');
    }
    
    public function posts($postTypeArr){
  
        return $this->hasMany('App\Model\Posts', 'user_id')->whereIn('post_type',$postTypeArr)->orderBy("id",'desc');
    }
    
    public function postBookmark(){
        return $this->hasMany('App\Model\PostBookmark', 'user_id');
    }
    
     public function followings(){
        return $this->hasMany('App\Model\FollowUp', 'following_uid');
    }
   
    public function followers(){
        return $this->hasMany('App\Model\FollowUp', 'followers_uid');
    }
    public function followerRel()
    {
        $userData = Auth::User();
        return $this->hasOne('App\Model\FollowUp', 'followers_uid')->where('following_uid',$userData->id);
    }
    
    public function followingRel()
    {
        $userData = Auth::User();
        return $this->hasOne('App\Model\FollowUp', 'following_uid')->where('followers_uid',$userData->id);
    }
    public function notification() 
    {
        return $this->hasMany('App\Model\Notification','following_uid')->whereIn('type',[1,8,2,9])->orderBy("id",'desc');
    } 
    public function notificationoffollower() 
    {
        return $this->hasMany('App\Model\Notification','following_uid')->whereIn('type',[4])->orderBy("id",'desc');
    }
    public function notificationCount() 
    {
        return $this->hasMany('App\Model\Notification','following_uid')->where('is_read',0);
//        ->whereIn('type',[1,8,2,10]);
    }
     
//    public function getCreatedAtAttribute($value)
//    {
////        if(Auth::user()) {
//            return $this->UserTimezoneAware->appDate($value);
////            $date = Carbon::createFromFormat('Y-m-d H:i:s', $value);
////            $date->setTimezone(Auth::user()->timezone ? Auth::user()->timezone: "UTC");
////            return $date->toDateTimeString();
////        } else {
////            return $value;
////        }
//    }
//    public function getUpdatedAtAttribute($value)
//    {
//        
//        return $this->UserTimezoneAware->appDate($value);
//        
////        if(Auth::user()) {
////            $this->UserTimezoneAware->asDateTime($value);
//            // $date = Carbon::createFromFormat('Y-m-d H:i:s', $value);
//            // $date->setTimezone('Asia/Kolkata' ? 'Asia/Kolkata': "UTC");
//            // return $date->toDateTimeString();
////        } else {
////            return $value;
////        }
//    }
}
