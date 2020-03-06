<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Model\User;
use App\Model\FollowUp;
use App\Http\Traits\NotificationFollowing;
use App\Services\FcmTokenService;

class FollowUpsController extends Controller
{
    use NotificationFollowing;
    
    private $fcmTokenService;
    public function __construct(FcmTokenService $fcmTokenService) {
        $this->fcmTokenService = $fcmTokenService;
    }
    
    public function getFollowers(Request $request){
         try {
                    if ($request['userId']) {
                        $userData = User::findorfail($request['userId']);
                    } else {
                        $userData = Auth::User();
                    }
                        
                    $follower = $userData->followers;
                    
                    $followerCount = $userData->followers->count();
                    
                    $followData = $follower->map(function ($ip) {
                                  $abc = $ip->followingUser;
                                              $abc->followerRel; 
                                   return  $abc->bios;
                                   
                    });
                    return response()->json(['result' => $follower,'count' => $followerCount],200);
         }catch (Exception $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getFollowings(Request $request){
         try {
                if ($request['userId']) {
                        $userData = User::findorfail($request['userId']);
                    } else {
                        $userData = Auth::User();
                    }
                $following = $userData->followings;
                $followingCount = $userData->followings->count();

                $followData = $following->map(function ($ip) {
                    $abc = $ip->followerUser;
                                $abc->followingRel;
                    return $abc->bios;

                });

                return response()->json(['result' => $following,'count' => $followingCount],200);
         }catch (Exception $e) {
                 return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function followUpPeople(Request $request){
        $userData = Auth::User();
        try{
            if($request['status'] == 'true'){
              
               
                 DB::beginTransaction();
                    $data = FollowUp::where('following_uid',$userData->id)->where('followers_uid',$request['followUpId']);
                    $data->delete();
                 DB::commit();
            }else if($request['status'] == 'false'){
              
                DB::beginTransaction();
                    $post = FollowUp::create([
                                'following_uid' => $userData->id,
                                'followers_uid' => $request['followUpId'],
                    ]);
                    
                    //--add post Notification to users--//
                
                    $notificationData = [
                        'post_id'=>Null,
                        'user_id'=>$userData->id,
                        'post_type'=>4, 
                        'following_uid'=>$request['followUpId'],
                        'description'=>config('constants.NOTIFICATION_DESCRIPTION_TITLE'),
                    ];
                    $this->saveNotification($notificationData);

                    $notify = [
                        'user_ids'=> $request['followUpId'],
                        'title'=> $userData->user_name,
                        'description'=>config('constants.NOTIFICATION_DESCRIPTION_TITLE'),
                        'moredata'=> 'more'
                    ];
                    $this->fcmTokenService->sendNotification($notify);

                    //--add post Notification to users--//
                    
                    
                DB::commit();
            }
                $follower =  FollowUp::where('following_uid',$request['followUpId'])->where('followers_uid',$request['uid'])->count();
                return response()->Json(['status' => 'success','followStatus' =>$request['status'],'followResult' => $follower]);
       }catch (Exception $e) {
                return response()->json(['status' => $e->getMessage()], 500);
        }
    }
    
    public function getFolloFollowingResult(Request $request){
        $userData = Auth::User();
        $following = FollowUp::where('following_uid',$userData->id)->where('followers_uid',$request['profileId'])->count();
        
        $follower =  FollowUp::where('following_uid',$request['profileId'])->where('followers_uid',$userData->id)->count();
        if($following > 0){
            $status = "Unfollow";
        }else if ($follower > 0 && $following == 0){
            $status = 'Followback';
        }else{
            $status = 'Follow';
        }
        
         return response()->Json(['status' => $status]);
    }
}
