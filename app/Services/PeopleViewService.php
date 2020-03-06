<?php


namespace App\Services;

use DB;
use Auth;
use App\Model\User;
use App\Model\Speciality;
use App\Model\Languages;
use App\Model\Country;
use App\Model\State;
use App\Model\City;

class PeopleViewService {

  public function getUserData($request) {
        try {
            $userId = $request['userId'];
            $userData = User::find($userId);
            $specialityName = array();
            $address = array();

            $userProfileData = $userData->bios;

            $biosCount = count((array) $userProfileData);
            $langList = Array();

            if ($biosCount > 0) {
                $userData->bios->first();
//
                if ($userData->bios->specialities_id) {
                    $specialityName['primaryName'] = Speciality::where('id', $userData->bios->specialities_id)->first();
                }
                if ($userData->bios->secondary_specialities_ids) {
                    $spid = explode(',', $userData->bios->secondary_specialities_ids);
                    $secondSpName = Array();
                    foreach ($spid as $id) {
                        $secondSName = Speciality::where('id', $id)->first('name');
                        $secondSpName[] = $secondSName->name;
                    }
                    $specialityName['secondaryName'] = implode(',', $secondSpName);
                }

                if ($userData->bios->country_id) {
                    $address['country'] = Country::where('id', $userData->bios->country_id)->first();
                }
                if ($userData->bios->state_id) {
                    $address['state'] = State::where('id', $userData->bios->state_id)->first();
                }
                if ($userData->bios->city_id) {
                    $address['city'] = City::where('id', $userData->bios->city_id)->first();
                }
                if ($userData->bios->languages_id) {
                    $lid = explode(',', $userData->bios->languages_id);
                    foreach ($lid as $id) {
                        $name = Languages::where('id', $id)->first('name');
                        $langList[] = $name->name;
                    }
                    $langList['lang'] = implode(', ', $langList);
                }
            }


            if ($userData->user_type == '1') {
                $additional = $userData->additional;
                if (count((array) $additional) > 0) {
                    $userProfileData = $userData->additional->first();
                }
            }
            $this->UserArrayData = array_merge(array("userData" => $userData), array("speciality" => $specialityName), array('address' => $address), array('lang' => $langList));
            return $this->UserArrayData;
        } catch (\Exception $e) {
            return $e;
        }
    }


}
?>