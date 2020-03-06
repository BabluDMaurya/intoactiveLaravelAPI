<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return [
        	'id' => $this->id,
        	'user_id' => $this->user_id,
                'display_name' => $this->display_name,
        	'about_me' => $this->about_me,
        	'hometown' => $this->hometown,
        	'profile_pic' => $this->profile_pic,
        	'profile_background_image' => $this->profile_background_image,
        	'specialities_id' => $this->specialities_id,
        	'secondary_specialities_ids' => $this->secondary_specialities_ids,
        	'languages_id' => $this->languages_id,
        	'birth_year' => $this->birth_year,
        	'gender' => $this->gender,
        	'country_id' => $this->country_id,
        	'state_id' => $this->state_id,
        	'city_id' => $this->city_id,
        ];
    }
}
