<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\UserTimezoneAware;
use Auth;

class Bio extends Model
{
    use UserTimezoneAware;
    protected $fillable = [
       'user_id', 'display_name', 'about_me', 'hometown','profile_pic', 'profile_background_image', 'specialities_id' , 'secondary_specialities_ids', 'languages_id','birth_year','country_id','state_id','city_id','gender','user_type'
    ];
    
//    public function getProfilePicAttribute($value)
//    { 
//        if(!empty($value)){
//            $userData = Auth::user();
//            return asset('/storage/profile_pic/'.$userData->id.'/'.$value);
//        }
//        return ;
//    }
//    public function getProfileBackgroundImageAttribute($value)
//    {
//         if(!empty($value)){
//         $userData = Auth::user();
//            return asset('/storage/profile_background_image/'.$userData->id.'/'.$value);
//             }
//             return ;
//    }
    
}
