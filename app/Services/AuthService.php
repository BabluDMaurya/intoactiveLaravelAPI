<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Model\User;
use App\Mail\UserOtp;
use App\Mail\UpdatePassword;
use App\Mail\WelcomeEmail;
use App\Http\Requests\RegistrationRequest;
use App\Jobs\RegistrationEmailJobs;
use App\Jobs\UpdatePasswordEmailJobs;
use App\Jobs\CheckOtpJobMail;
use App\Jobs\WelcomeEmailJobs;
use App\Jobs\ResendOtpEmailJobs;
use App\Jobs\ForgotPasswordEmailJobs;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Model\Bio;
use App\Model\InviteUsers;
use App\Http\Traits\UserTimezoneAware;
use App\Http\Traits\GeoPluginable;
use App\Services\FcmTokenService;

class AuthService {

    use AuthenticatesUsers,
        GeoPluginable,
        UserTimezoneAware;

    protected $maxAttempts = 5;
    protected $decayMinutes = 2;
    public $otp;
    public $ipAddress;
    public $ipCountry;
    public $timezone;
    public $user;
    public $userData;
    public $data;
    public $email;
    public $job;
    public $date;
    public $now;
    public $diff;
    public $status;
    public $credentials;
    public $token;
    private $fcmTokenService;
    
    public function __construct(FcmTokenService $fcmTokenService) {
        $this->fcmTokenService = $fcmTokenService;
    }
    
    public function userRegistration($request) {
      
        DB::beginTransaction();
        try {
            $this->otp = mt_rand(100000, 999999);
            $this->ipAddress = \Request::getClientIp(true);
            
            // --------------  need an api for refurn timezone according to latitude and longitude --------------------//
      
//            if (!empty($request['lat']) && !empty($request['lon'])) {
//                $this->addrDetailsArr = $this->addressDetailsFromLatLon($request['lat'], $request['lon']);
//            } else {
                $this->addrDetailsArr = $this->addressDetailsFromIP($this->ipAddress);
                
//            }
            $this->ipCountry = $this->addrDetailsArr['geoplugin_countryName'];
            $this->timezone = $this->addrDetailsArr['geoplugin_timezone'];
            $inviteUserData = InviteUsers::where('email', $request['email'])->first();
//            print_r( $this->inviteUserData);
//              die('ssasa');
            //Insert Record in User Table
            $this->user = User::create([
                        'user_name' => $request['username'],
                        'email' => $request['email'],
                        'password' => bcrypt($request['password']),
                        'gender' => $request['sex'],
                        'user_type' => ($inviteUserData) ? intval($inviteUserData->is_invited) : 0,
                        'otp_code' => $this->otp,
                        'location' => $this->ipCountry,
                        'ip_address' => $this->ipAddress,
                        'timezone' => strval($this->timezone),
            ]);
            //Insert Record in Bio Table with the last inserted id
            Bio::create([
                'user_id' => $this->user->id,
                'gender' => $request['sex'],
                'user_type' => 0
            ]);
            DB::commit();
            $this->data = ['message' => $this->otp,  'userName'=>$request['username'],'subject'=> 'OTP for validate the email address.' ];
            $this->data['content'] = 'Please use this otp to validate your Email.';
            $this->email = $request['email'];
            
            Mail::to($this->email)->send(new UserOtp($this->data));
            
//            $this->job = (new RegistrationEmailJobs($this->data, $this->email));
//            dispatch($this->job);
            return $this->user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    
    public function checkUnameAvaibility ($uname){
        try {
            $this->count = User::where('user_name', $uname)->count();
            if ($this->count > 0) {
                $this->status = false;
                $sugg_res = array();
                for ($i = 1; $i <= 4; $i++) {
                    $suggestName = $uname . mt_rand(0, 1000);
                    $this->checkavailable = User::where('user_name', $suggestName)->count();
                    if ($this->checkavailable == 0) {
                        $sugg_res[] = $suggestName;
                    }
                }
                return response()->json(['status' => $this->status, 'usernameSuggetions' => $sugg_res], 201);
            } else {
                $this->status = true;
                return response()->json(['status' => $this->status], 201);
            }
        }catch(\Exception $e){
             throw $e;
        }
    }

    public function checkOtp($request) {
        try {

            $this->otp = User::where('is_delete', '0')->where('email', $request->uemail)->first();

            $this->date = Carbon::parse($this->otp->updated_at);
            $this->now = Carbon::now();
            $this->diff = $this->date->diffInMinutes($this->now);

            if (!empty($this->otp['otp_code']) && $this->otp['otp_code'] == $request->otp && $this->diff < 15) {
                DB::beginTransaction();
                $this->otp->update([
                    'is_verified' => '1',
                    'otp_code' => null
                ]);
                DB::commit();

                $this->data = ['userName' => $this->otp['user_name']];
                $this->email = $request->uemail;
                
                Mail::to($this->email)->send(new WelcomeEmail($this->data));
                
//                $this->job = (new WelcomeEmailJobs($this->data, $this->email));
//                dispatch($this->job);

                $this->status = true;
            } elseif ($this->diff > 15) {
                $this->status = "Otp Has Expired";
            } else {
                $this->status = false;
            }
            return $this->status;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function resendOtp($request) {
        try {
            $this->userData = User::where('email', $request['uemail'])->first();
            if ($this->userData) {
                $this->otp = mt_rand(100000, 999999);
                DB::beginTransaction();
                $this->userData->update([
                    
                    'otp_code' => $this->otp,
                ]);
                DB::commit();
                $this->data = ['message' => $this->otp , 'userName'=> $this->userData->user_name, 'subject'=> 'OTP for validate the email address.'];
                $this->email = $request['uemail'];
                $this->data['content'] = 'Please use this otp to validate your Email.';
                
                Mail::to($this->email)->send(new UserOtp($this->data));
                
//                $this->job = (new ResendOtpEmailJobs($this->data, $this->email));
//                dispatch($this->job);
                $this->status = true;
            } else {
                $this->status = 'Email  not found';
            }
            return $this->status;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function userLogin($request) {
        try {
            if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                $this->credentials = ['email' => $request->username, 'password' => $request->password, 'is_delete' => '0'];
            } else {
                $this->credentials = ['user_name' => $request->username, 'password' => $request->password, 'is_delete' => '0'];
            }
            //check if user has reached the max number of login attempts
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return response()->json(['message' => 'You Enter 5 wrong login attempt limit. Try again after 2 min'], 400);
            }

            if (Auth::attempt($this->credentials)) {

                $this->userData = Auth::user();
                if ($this->userData->is_verified == 0) {
                    return response()->json(['message' => 2, 'uemail' => $this->userData['email']], 400);
                } elseif ($this->userData->is_active == 0) {
                    return response()->json(['message' => 'User account is deactivated'], 400);
                }
                //reset failed login attemps
                $this->clearLoginAttempts($request);

                $this->token = $this->userData->createToken('TutsForWeb')->accessToken;
                return response()->json(['userData' => $this->userData, 'userToken' => $this->token, 'message' => 'Successful'], 200);
            } else {
                //count user failed login attempts
                $this->incrementLoginAttempts($request);
                return response()->json(['message' => 'Invailid username or password'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function forgotPassword($request) {
        try {

            $this->userData = User::where('is_delete', '0')->where('email', $request->uemail)->where('is_verified', '1')->first();
            if (!empty($this->userData)) {
                $this->otp = mt_rand(100000, 999999);

                DB::beginTransaction();
                $this->userData->update([
                    'otp_code' => $this->otp,
                ]);
                DB::commit();
                
                $this->status = true;
                $this->data = ['message' => $this->otp , 'userName'=>$this->userData->user_name];
                $this->email = $request['uemail'];
                
                 $this->data['content'] = 'Please use this otp to recover your password.';
                 $this->data['subject'] = 'New Otp for forgot Password';
                 Mail::to($this->email)->send(new UserOtp($this->data));
        
//                $this->job = (new ForgotPasswordEmailJobs($this->data, $this->email));
//                    dispatch($this->job); 

            } else {
                $this->status = 'Email not found';
            }
            return $this->status;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updatePassword($request) {
        $this->userData = User::where('is_delete', '0')->where('email', $request->uemail)->where('is_verified', '1')->first();

        if ($this->userData) {
            $this->date = Carbon::parse($this->userData->updated_at);
            $this->now = Carbon::now();
            $this->diff = $this->date->diffInMinutes($this->now);

            if (!empty($this->userData['otp_code']) && $this->userData['otp_code'] == $request->ucode && $this->diff < 15) {
            
                DB::beginTransaction();
                $this->userData->update([
                    'password' => bcrypt($request->cPassword),
                    'otp_code' => null
                ]);
                DB::commit();
                  
                $data = ['userName'=>$this->userData->user_name,'subject'=>'Password Change Successfully' ];               
                $email = $request->uemail;                
                Mail::to($email)->send(new UpdatePassword($data));
                
//                $job = (new UpdatePasswordEmailJobs($data, $email));
//                dispatch($job);                
                $this->status = true;                
                
            } elseif ($this->diff > 15) {
                $this->status = 'Otp Has Expired';
            } elseif ($this->userData['otp_code'] != $request->ucode) {            
                $this->status = 'Otp Doesnt matched';
            }
        } else {
            $this->status = 'Email  not found';
        }
        return $this->status;
    }

}
