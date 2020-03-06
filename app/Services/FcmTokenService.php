<?php

namespace App\Services;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Model\FcmToken as UserFcmTokenModel;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class FcmTokenService {

    use AuthenticatesUsers;

    private $FCMTokenData;
    private $data;

    public function fcmTokenStore(Request $request) {        
        $userId = Auth::user()->id;        
        DB::beginTransaction();        
        try {
             $userAvailabel = UserFcmTokenModel::where('user_id', $userId)->first();    
             $this->data = $request->all();
            if($userAvailabel == null){                
                if (array_key_exists('device_type', $this->data)) {
                    $this->FCMTokenData = ['user_id' => $userId,
                        'apns_id' => $this->data['token']];
                    // Add the IOS device token to dbs
                } else {
                    $this->FCMTokenData = ['user_id' => $userId,
                        'token' => $this->data['token']];
                    // Add the android device token to dbs
                }
                UserFcmTokenModel::create($this->FCMTokenData);
                return TRUE;
            }else{               
                if (array_key_exists('device_type', $this->data)) {
                    $this->FCMTokenData = ['apns_id' => $this->data['token']];
                    // Add the IOS device token to dbs
                } else {
                    $this->FCMTokenData = ['token' => $this->data['token']];
                    // Add the android device token to dbs
                }
                UserFcmTokenModel::where('user_id', $userId)->update($this->FCMTokenData);
                return TRUE;
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

//    public function fcmTokenUpdate(Request $request) {
//        $userId = Auth::id();
//        DB::beginTransaction();
//        try {
//            $this->data = $request->all();
//            if (array_key_exists('device_type', $this->data)) {
//                $this->FCMTokenData = ['apns_id' => $this->data['token']];
//                // Add the IOS device token to dbs
//            } else {
//                $this->FCMTokenData = ['token' => $this->data['token']];
//                // Add the android device token to dbs
//            }
//            UserFcmTokenModel::where('user_id', $userId)->update($this->FCMTokenData);
//            return TRUE;
//        } catch (\Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//    }
    public function sendNotificationRequest(Request $request) {        
        $datas = [
                    'user_ids'=> $request->user_ids,
                    'title'=>$request->title,
                    'description'=>$request->discription,
                    'moredata'=>$request->moredata
                ];
        return $this->sendNotification($datas);        
    }

    public function sendNotification($data) {        
        DB::beginTransaction();

        try {
            $tokens = [];
            $apns_ids = [];
            $responseData = [];           
            $users = explode(',', $data['user_ids']);
            // for Android            
            if ($this->FCMTokenData = UserFcmTokenModel::whereIn('user_id', $users)->select('token')->where('token', '!=', null)->get()) {
                foreach ($this->FCMTokenData as $key => $value) {
                    $tokens[] = $value->token;
                }
                $notificationData = array(
                    'title' => $data['title'],
                    'body' => $data['description'],
                    'sound' => true,
                );
                    if(!empty($data['moredata'])){
                        $moredata = $data['moredata'];
                    }else{
                        $moredata = 'more';
                    }
                $extraNotificationData = ["message" => $notificationData, "moredata" => $moredata];
                $fields = array(
                    'registration_ids' => $tokens,
                    'notification' => $notificationData,
                    'data' => $extraNotificationData
                );
                $headers = array(
                    'Authorization: key=' . config('app.fcm_legacy_key'),
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('app.fcm_api_url'));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    return back()->withError('FCM Send Error: ' . curl_error($ch));
                }
                $result = json_decode($result, true);
                $responseData['android'] = [
                    "result" => $result
                ];
                curl_close($ch);
            }
            // for IOS
            if ($this->FCMTokenData = UserFcmTokenModel::whereIn('user_id', $users)->where('token', '!=', null)->select('token')->get()) {
                foreach ($this->FCMTokenData as $key => $value) {
                    $apns_ids[] = $value->apns_id;
                }
                $title = $data['title'];
                $body = $data['description'];
                $notification = array('title' => $title, 'text' => $body, 'sound' => 'default', 'badge' => '1');
                $arrayToSend = array('registration_ids' => $apns_ids, 'notification' => $notification, 'priority' => 'high');
                $json = json_encode($arrayToSend);
                $headers = array();
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Authorization: key=' . config('app.fcm_legacy_key');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, config('app.fcm_api_url'));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //Send the request
                $result = curl_exec($ch);
                if ($result === FALSE) {
                    die('FCM Send Error: ' . curl_error($ch));
                }
                $result = json_decode($result, true);
                $responseData['ios'] = [
                    "result" => $result
                ];
                //Close request
                curl_close($ch);
            }
//            return $responseData;
            return response()->json(['message' => TRUE], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
