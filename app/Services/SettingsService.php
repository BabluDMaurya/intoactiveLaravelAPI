<?php

namespace App\Services;

use DB;
use Mail;
use Auth;
use Exception;
use Carbon\Carbon;
use App\Model\User;
use App\Model\ContactAdmin;
use App\Model\Bio;
use App\Model\AdditionalInfo;
use App\Mail\AdminMail;
use App\Jobs\ContactAdminEmailJobs;
use App\Model\Country;
use App\Model\State;
use App\Model\City;
use App\Model\Speciality;
use App\Model\Languages;

class SettingsService {

    public $userData;
    public $data;
    public $email;
    public $userProfileData;
    public $additionalData;
    public $stateData;
    public $cityData;
    public $UserArrayData;
    public $status;
    public $user;
    public $job;

    public function contactAdmin($request) {
        try {
            $this->userData = Auth::User();
            DB::beginTransaction();
            ContactAdmin::create([
                'uid' => $this->userData->id,
                'query_type' => $request['query'],
                'subject' => $request['subject'],
                'message' => $request['message'],
            ]);
            DB::commit();
            $this->data = ['message' => $request['message'],
                'subject' => $request['subject'],
                'queryType' => $request['query'],
                'uid' => $this->userData->user_name
            ];
            $this->email = config('constants.ADMIN_EMAIL');
            $this->job = (new ContactAdminEmailJobs($this->data, $this->email));
            dispatch($this->job);
            $this->status = TRUE;
            return $this->status;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function resetPassword($request) {
        try {
            $this->user = Auth::User();
            if (\Hash::check($request->cur_pass, $this->user->password)) {
                $this->user->password = \Hash::make($request->cnf_pass);
                $this->user->save();
                $this->status = true;
            } else {
                $this->status = 'Wrong Current Password entered';
            }
            return $this->status;
        } catch (\Exception $e) {
            return response()->json(['status' => $e->getMessage()], 500);
        }
    }

    public function profileData() {
      try {

            $user = Auth::User();
            $specialityName = array();
            $address = array();

            $userProfileData = $user->bios;

            $biosCount = count((array) $userProfileData);
            $langList = Array();

            if ($biosCount > 0) {
                $user->bios->first();

                if ($user->bios->specialities_id) {
                    $specialityName['primaryName'] = Speciality::where('id', $user->bios->specialities_id)->first();
                }
                if ($user->bios->secondary_specialities_ids) {
                    $secSpecIds = $user->bios->secondary_specialities_ids;
                    $spid = explode(',', $secSpecIds);
                   
                    $secondSpName = Array();
                    foreach ($spid as $id) {
                       
                        $secondSName = Speciality::where('id', $id)->first();
                     
                        $secondSpName[] = $secondSName->name;
                        
                    }
                  
                    $specialityName['secondaryName'] = implode(',', $secondSpName);
                   
                }

                if ($user->bios->country_id) {
                    $address['country'] = Country::where('id', $user->bios->country_id)->first();
                    $address['allStates'] = State::where('country_id', $user->bios->country_id)->get();
                }
                
                if ($user->bios->state_id) {
                    $address['state'] = State::where('id', $user->bios->state_id)->first();
                    $address['allCities'] = City::where('state_id', $user->bios->state_id)->get();  
                }
                if ($user->bios->city_id) {
                    $address['city'] = City::where('id', $user->bios->city_id)->first();
                }
                if ($user->bios->languages_id) {
                    $lid = explode(',', $user->bios->languages_id);
                    foreach ($lid as $id) {
                        $name = Languages::where('id', $id)->first('name');
                        $langList[] = $name->name;
                    }
                    $langList['lang'] = implode(', ', $langList);
                }
            }


            if ($user->user_type == '1') {
                $additional = $user->additional;
                if (count((array) $additional) > 0) {
                    $userProfileData = $user->additional->first();
                }
            }
            
            
            $this->UserArrayData = array_merge(array("userData" => $user), array("speciality" => $specialityName), array('address' => $address), array('lang' => $langList));
            return $this->UserArrayData;
       } catch (\Exception $e) {
           return response()->json(['status' => $e->getMessage()], 500);
       }
    }

}
