<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\User;
use App\Model\Bio;
use App\Model\AdditionalInfo;
use App\Model\ClassName;
use App\Model\Speciality;
use App\Model\Languages;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
use App\Model\FcmToken as UserFcmTokenModel;
use App\Model\OauthAccessToken;
use App\Services\SettingsService;
use App\Model\ContactAdmin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Traits\UserTimezoneAware;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserData;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Support\MessageBag;

class SettingsController extends Controller {
     use AuthenticatesUsers,UserTimezoneAware;
    
    protected $settingsService;
    public $data;
    public $date;
    public $time;
    public $dateAccordingToTimezone;
    public $timeAccordingToTimezone;
    public $timezone;
    public $userId;
    public $cityData;
    public $stateData;
    public $classData;
    public $specialityData;
    public $langData;
    public $countryData;
    public $userData;
    public $channel;
    public $fileName;
    public $picture;
    private $bioData; 
    
    public function __construct(SettingsService $settingsService) {
        $this->settingsService = $settingsService;
        $this->userData = Auth::user();
    }

    public function contactAdmin(Request $request) {
        try {
            $this->data = $this->settingsService->contactAdmin($request);
            return response()->json(['status' => $this->data], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteUser(Request $request) {
        try {
            $this->userId = User::where('id', $request->uid)->first();
            DB::beginTransaction();
            $this->userId->update(['is_delete' => 1]);
            DB::commit();
            return response()->json(['message' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logOut() {
        
        if (Auth::check()) {
            $userId = Auth::user()->id;
            UserFcmTokenModel::where('user_id', $userId)->update(['token'=>NULL,'apns_id'=>NULL]);
            Auth::user()->AauthAcessToken()->delete();
            return response()->json(['status' => true], 200);
        }
    }

    public function resetPassword(Request $request) {
        try {
            $this->data = $this->settingsService->resetPassword($request);
            return response()->json(['status' => $this->data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 200);
        }
    }

    public function currentTimeZone() {
        $this->timezone = $this->userData['timezone'];
        // Stored date and time
        $this->time = date('H:i:s', strtotime($this->userData['created_at']));
        $this->date = date('Y-m-d', strtotime($this->userData['created_at']));
        // convert date and time according to user TimeZone
        $this->dateAccordingToTimezone = $this->getDate(date('Y-m-d'));
        $this->timeAccordingToTimezone = $this->getTime(date('H:i:s'));

        return response()->json(['timezone' => $this->timezone, 'created_at' => $this->userData['created_at'], 'db_date' => $this->date, 'db_time' => $this->time, 's_time' => $this->timeAccordingToTimezone, 's-date' => $this->dateAccordingToTimezone], 200);
    }

    public function editProfile(Request $request) {
        $this->userData = Auth::user();
        try {            
            if($request->field_name == 'about_me') {
              
                $messages = [
                    'max'    => 'Can not type more than 1000 Characters',
                ];
                $validator = Validator::make($request->all(), [
                    'field_data' => 'max:1000',
                ],$messages)->validate();
            }
            
            if($request->field_name == 'display_name') {
              
                $messages = [
                    'max'    => 'Can not type more than 12 Characters',
                ];
                $validator =  Validator::make($request->all(), [
                    'field_data' => 'max:12',
                ],$messages)->validate();
            }
            
            if($request->field_name == 'hometown') {
              
                $messages = [
                    'max'    => 'Can not type more than 200 Characters',
                ];
                $validator = Validator::make($request->all(), [
                    'field_data' => 'max:200',
                ],$messages)->validate();
            }
//            $errors = $validator->errors();
//            if ($errors) {    
//                return response()->json($validator->messages(), 200);
//            }
            $this->userId = User::where('id', $this->userData->id)->first();
            $this->bioUserId = Bio::where('user_id', $this->userData->id)->first();
            DB::beginTransaction();
            if($request->field_name == 'birth_year'){ 
            $this->userId->update([$request->field_name => $request->field_data]);
            }
            if($request->field_name == 'gender'){
                
                $this->userId->update([$request->field_name => $request->field_data]);
            }
            $this->bioUserId->update([$request->field_name => $request->field_data]);
            DB::commit();
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function additionalInfo( Request $request) {
        
        try {                
                 $this->userData = Auth::user();
                 if($request->field_name == 'tag_line') {
              
                $messages = [
                    'max'    => 'Can not type more than 50 Characters',
                ];
                $validator = Validator::make($request->all(), [
                    'field_data' => 'max:50',
                ],$messages)->validate();
            }
               $count  = AdditionalInfo::where('user_id', $this->userData->id)->count();
               if($count <= 0){
                    DB::beginTransaction();
                        AdditionalInfo::create([
                            'user_id' => $this->userData->id,
                            $request->field_name => $request->field_data,
                        ]);
                    DB::commit();
               }
                $this->userId = AdditionalInfo::where('user_id', $this->userData->id)->first();
                DB::beginTransaction();
                $this->userId->update([$request->field_name => $request->field_data]);
                DB::commit();
                return response()->json(['status' => 'success'], 200);
           
            
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function uploadVideo(Request $request){
        if ($request->hasFile('video')) {
            $request->file('video')->storeAs('public/profile_pic/', date('His').'bablu.mp4');
            return response()->json(['status' => 'success'], 200);
        }else{
            return response()->json(['status' => 'fail'], 500);
        }
    }
    
    public function uploadPic(Request $request) {
        $this->userData = Auth::user();        
        if ($request->hasFile('file')) { 
            
            $this->field_name = $request->channel;
            $this->fileName = 'intoactive';
            $this->picture = date('His') . '-' . $this->fileName . '.jpeg';
            $picture  = 'public/profile_pic/'.date('YmdHis').'.jpeg';
            
            
//            if ($this->field_name == 'profile_pic') {
//                 $request->file('file')->move(base_path('public/profile_pic'), $this->picture);                                   
//            } else if ($this->field_name == 'profile_background_image') {                
//                $request->file('file')->move(base_path('public/profile_background_image'), $this->picture);                 
//            }
            if ($this->field_name == 'profile_pic') {
                $thumbPath = 'public/profile_pic/thumb/'.$this->picture;
            $this->compressImage($request->file('file')->move(base_path('public/profile_pic'), $this->picture) ,$thumbPath,55);
            }else if($this->field_name == 'profile_background_image'){
                $thumbPath = 'public/profile_background_image/thumb/'.$this->picture;
                $this->compressImage($request->file('file')->move(base_path('public/profile_background_image'), $this->picture) ,$thumbPath,55);
            }
            
            $this->userId = Bio::where('user_id', $this->userData->id )->first();            
            DB::beginTransaction();
            $this->userId->update([$this->field_name => $this->picture]);            
            DB::commit();
            return response()->json(['status' => 'success'], 200);
        }
    }
    public function getProfileData() {
        try {
            $this->data = $this->settingsService->profileData();
            return response()->json(['status' => $this->data], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getState(Request $request) {
        try {
            $this->stateData = State::where('country_id', $request->field_data)->get();
            return response()->json(['stateData' => $this->stateData], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getCity(Request $request) {
        try {
            $this->cityData = City::where('state_id', $request->field_data)->get();
            return response()->json(['cityData' => $this->cityData], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getCommonData() {
        try {
            $this->classData = ClassName::get();
            $this->specialityData = Speciality::get();
            $this->langData = Languages::get();
            $this->countryData = Country::get();
            return response()->json(['classData' => $this->classData, 'specialityData' => $this->specialityData, 'langData' => $this->langData, 'countryData' => $this->countryData], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public  function getMyprofileData(){
        try{
                $this->userId = Auth::User();
                $this->bioData = Bio::where('user_id', $this->userId->id)->first();
                
                
                if($this->bioData->country_id != ''){
                    $this->countryData = Country::where('id',$this->bioData->country_id)->first();
                }else{
                    $this->countryData = NULL;
                }
                
                
                if(isset($this->bioData->state_id)){
                    $this->stateData = State::where('id',$this->bioData->state_id)->first();
                } else {
                    $this->stateData= NULL;
                }
                
                if(isset($this->bioData->city_id)){
                    $this->cityData = City::where('id',$this->bioData->city_id)->first();
                } else {
                    $this->cityData = NULL;
                }
                 
                if(isset($this->bioData->specialities_id)){
                    $this->specialityData = Speciality::where('id',$this->bioData->specialities_id)->first();
                }else{
                    $this->specialityData = NULL;
                }
                return response()->json(['countryData' => $this->countryData, 'stateData' => $this->stateData, 'cityData' => $this->cityData, 'specialities' => $this->specialityData],200);
                
        } catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    function compressImage($source, $destination, $quality) {

        $info = getimagesize($source);

       // if ($info['mime'] == 'image/jpeg') {
          $image = imagecreatefromjpeg($source);
        //}
      

        imagejpeg($image, $destination, $quality);

}

}
