<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePassword extends FormRequest
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
            'otp' => 'required|min:6|max:6',
            'newPassword' => 'min:6|confirmed',
            'cPassword' => 'min:6|required_with:cPassword'
        ];
    }

     public function messages()
    {
        return[
            'otp' => 'Please enter Otp !',
            'newPassword' => 'Password Eror',
            'cPassword' => 'Password Eror'
        ];
    }
}
