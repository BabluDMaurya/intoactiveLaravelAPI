<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\User;
use App\Mail\UserOtp;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpRequest;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Requests\ResetPassword;

class AuthController extends Controller {

    use AuthenticatesUsers;

    protected $authService;
    protected $count;
    protected $status;
    protected $checkavailable;
    protected $data;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }    
    
    public function register(RegistrationRequest $request) {
        try {
            $this->authService->userRegistration($request);
            return response()->json(['message' => true], 201);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage()], 500);
        }
    }
    
    public function checkEmailAvaibility($email) {
        try {
            $this->count = User::where('is_delete', '0')->where('email', $email)->count();
            if ($this->count > 0) {
                $this->status = false;
            } else {
                $this->status = true;
            }
            return response()->json(['status' => $this->status], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function checkUnameAvaibility($uname) {
        try {
            $this->data = $this->authService->checkUnameAvaibility($uname);
            return $this->data;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function checkOtp(OtpRequest $request) {
        try {
            $this->data = $this->authService->checkOtp($request);
            return response()->json(['status' => $this->data], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function resendOtp(Request $request) {
        try {
            $this->data = $this->authService->resendOtp($request);
            return response()->json(['status' => $this->data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function userLogin(LoginRequest $request) {
        try {
            $this->data = $this->authService->userLogin($request);
            return $this->data;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 200);
        }
    }
    
    public function forgotPassword(ForgotPasswordRequest $request) {
        try {
            $this->data = $this->authService->forgotPassword($request);
            return response()->json(['status' => $this->data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function updatePassword(Request $request) {
        try {
            $this->data = $this->authService->updatePassword($request);
            return response()->json(['status' => $this->data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    
    public function testt() {
        try {
            $userDetails = Auth::user();
            print_r($userDetails);
            return response()->json(['message' => 'Hello World'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 200);
        }
    }
    
    public function captchaHtml() {
        return response()->json(['value1' => app('mathcaptcha')->label(), 'result' => app('mathcaptcha')->getMathResult()], 200);
    }
    
}
