<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'about_me'      => 'max:10',
            'about_me.*'      => 'max:10',
            'tag_line.*'      => 'max:200',
            'hometown.*' =>'max:200',
            'display_name.*' =>'max:12',
        ];
    }
    
     public function messages()
     {
         return [
             'about_me.max' => 'Please enter only 1000 characters !',
             'tag_line.max' => 'Please enter only 200 characters !',
             'hometown.max' => 'Please enter only 200 characters !',
             'display_name.max' => 'Please enter only 12 characters !',
            
         ];
     }
}
