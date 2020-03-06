<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\FcmTokenService;

class SendPushNotificationController extends Controller {

    private $fcmTokenService;
    private $data;

    public function __construct(FcmTokenService $fcmTokenService) {
        $this->fcmTokenService = $fcmTokenService;
    }

    public function send(Request $request){        
        try {
            return $this->fcmTokenService->sendNotificationRequest($request);            
        } catch (\Exception $e) {
            return response()->json(['message' => FALSE], 400);
        }
    }

    

}
