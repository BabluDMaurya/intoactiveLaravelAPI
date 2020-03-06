<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FCMTokenRequest;
use App\Services\FcmTokenService;

class UserFcmTokenControlle extends Controller {

    private $fcmTokenService;
    private $data;

    public function __construct(FcmTokenService $fcmTokenService) {
        $this->fcmTokenService = $fcmTokenService;
    }

    public function store(FCMTokenRequest $request) {
        try {
            $this->data = $this->fcmTokenService->fcmTokenStore($request);
            return response()->json(['message' => $this->data], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => FALSE], 400);
        }
    }
//    public function update(FCMTokenRequest $request) {
//        try {
//            $this->data = $this->fcmTokenService->fcmTokenUpdate($request);
//            return response()->json(['message' => $this->data], 200);
//        } catch (\Exception $e) {
//            return response()->json(['message' => FALSE], 400);
//        }
//    }

}
