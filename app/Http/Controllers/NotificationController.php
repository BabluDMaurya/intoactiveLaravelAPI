<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(NotificationService $notificationService) {
        $this->notificationService = $notificationService;
    }
    public function showNotification(Request $request) {
        try {
            $data = $this->notificationService->showNotif($request);
            return response()->json(['status' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }  
    public function showNotificationOfFollower(Request $request) {
        try {
            $data = $this->notificationService->showNotifOfFollower($request);
            return response()->json(['status' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    } 
    public function showUnreadNotification(Request $request) {
        try {
            $data = $this->notificationService->showUnreadNotif($request);
            return response()->json(['status' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }
    public function setReadNotification(Request $request) {
        try {
            $data = $this->notificationService->setReadNotifi($request);
            return response()->json(['status' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }
}
