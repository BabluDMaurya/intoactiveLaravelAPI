<?php

namespace App\Services;
use DB;
use Auth;
use App\Model\Notification;
use App\Model\User;
use Carbon\Carbon;
class NotificationService {   

    public function __construct() {
        
    }    
    public function showNotif($request) {
        $userData = Auth::user();
        DB::beginTransaction();
        try {                       
            $notification = $userData->notification;
            $notification->map(function ($noti) {
                 return $noti->user->bios;
            });
            DB::commit();
            return($notification);            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
     public function showNotifOfFollower($request) {
        $userData = Auth::user();
        DB::beginTransaction();
        try {                       
            $notification = $userData->notificationoffollower;
            $notification->map(function ($noti) {
                 return $noti->user->bios;
            });
            DB::commit();
            return($notification);            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function showUnreadNotif($request) {
        $userData = Auth::user();
        DB::beginTransaction();
        try {                       
            $notificationCount = $userData->notificationCount;  
            DB::commit();            
            return($notificationCount);            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function setReadNotifi($request) {        
        $userData = Auth::user();
        DB::beginTransaction();
        try {
            Notification::where('following_uid', '=', strval($userData->id))
                ->update([
                    'is_read' => '1',
                ]);
            
            //-----deleting the 30 day's old notification form database
            Notification::where('created_at', '<=', Carbon::now()->subDays(30)->toDateTimeString())->delete();
            DB::commit();
            return True;            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
   
}
