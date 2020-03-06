<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\User;
use App\Model\Bio;
use App\Model\ClassName;
use App\Model\Speciality;
use App\Model\Languages;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
use App\Services\MyProfileService;
use App\Model\OauthAccessToken;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Request;
use App\Http\Resources\UserData;


class MyProfileController extends Controller
{
    use AuthenticatesUsers;
    protected  $myProfileService;
    public $userId;
    public $cityData;
    public $stateData;
    public $classData;
    public $specialityData;
    public $langData;
    public $countryData;
    public $userData;
    public $bioData;
    public $myProfileData;
    
    public function __construct(MyProfileService $myProfileService) {
        $this->myProfileService = $myProfileService;
        $this->userData = Auth::user();
    }
    
    public  function getMyProfileData(){
        try{
                $this->myProfileData = $this->myProfileService->myProfileData();
                return response()->json(['status' => $this->myProfileData], 200);
        } catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function getProfileImage(){
            $this->userId = Auth::user();
            $userData = Bio::where('user_id', $this->userId->id)->first();
            return response()->json(['status' => $userData], 200);
    }
}
