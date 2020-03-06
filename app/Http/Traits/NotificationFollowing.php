<?php
namespace App\Http\Traits;
use Auth;
use App\Model\FollowUp;
use App\Model\Notification;

trait NotificationFollowing{
    
    public function followingId() {        
        $followersId = array();        
        $folloUp = FollowUp::where('followers_uid',Auth::user()->id)->select('following_uid')->get();        
        if(count($folloUp) > 0){
        foreach($folloUp as $value){
             $followersId[] = $value->following_uid;
        }
        return $result = implode(',', $followersId);        
        }else{
          return FALSE;  
        }
    }
    public function saveNotification($data){        
        $followingIds = explode(',',$data['following_uid']);
        foreach ($followingIds as $fid){
            Notification::create([
                'posts_id' => $data['post_id'],
                'user_id' => $data['user_id'],
                'following_uid' => $fid,
                'type'=> $data['post_type'],
                'is_read' => 0,
                'description'=>$data['description'],
            ]);
        }
    }
}