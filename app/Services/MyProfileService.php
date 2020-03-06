<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use Exception;
use App\Model\Bio;
use App\Model\ClassName;
use App\Model\Speciality;
use App\Model\Languages;
use App\Model\Country;
use App\Model\State;
use App\Model\City;

class MyProfileService {    
public $userData;
public $UserArrayData;
public $bioData;
public $countryData;
public $stateData;
public $cityData;
public $specialityData;
public $languageData;
public $secondarySpecId;

public function myProfileData(){
        try{
                $this->userData = Auth::User();
                $this->bioData = Bio::where('user_id', $this->userData->id)->first();
                
                
                if($this->bioData->country_id != ''){
                    $this->countryData = Country::where('id',$this->bioData->country_id)->first();
                }else{
                    $this->countryData = array();
                }
                
                
                if(isset($this->bioData->state_id)){
                    $this->stateData = State::where('id',$this->bioData->state_id)->first();
                } else {
                    $this->stateData= array();
                }
                
                if(isset($this->bioData->city_id)){
                    $this->cityData = City::where('id',$this->bioData->city_id)->first();
                } else {
                    $this->cityData = array();
                }
                 
                if(isset($this->bioData->specialities_id)){
                    $this->specialityData = Speciality::where('id',$this->bioData->specialities_id)->first();
                }else{
                    $this->specialityData = array();
                }
                
                $secondarySpecId = explode(',',$this->bioData->secondary_specialities_ids); 
                
                 if(isset($this->bioData->secondary_specialities_ids)){
                    // foreach($idsArr as $lang_id){
                    $this->secondarySpecId = Speciality::whereIn('id',$secondarySpecId)->get();
                     //}
                }else{
                    $this->secondarySpecId = array();
                }
                
                $idsArr = explode(',',$this->bioData->languages_id); 
                
                 if(isset($this->bioData->languages_id)){
                    // foreach($idsArr as $lang_id){
                    $this->languageData = Languages::whereIn('id',$idsArr)->get();
                     //}
                }else{
                    $this->languageData = array();
                }
                
                $this->UserArrayData = array_merge(array("countryData" => $this->countryData), array("stateData" => $this->stateData), array("cityData" => $this->cityData), array("specialityData" => $this->specialityData),array("secondarySpecialityData" => $this->secondarySpecId),array("languageData" => $this->languageData));
        
                return $this->UserArrayData;
                } catch (Exception $e){
            return response()->json(['status' => $e->getMessage()], 500);
        }   
    
    }
    
}