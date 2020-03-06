<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'email'    => 'required|string|email|emailformate|emaildomain|unique:users,email',
            'username' => 'required|string|min:5|max:10|unique:users,user_name|regex:/^[a-zA-Z][a-zA-Z\d-_\.]+$/',
            'password' => 'required|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',            
            'policy'   => 'required',
            'age'      => 'required',
            'sex'      => 'required|min:1|max:1',
        ];
    }

      public function messages()
     {
         return [
             'email.required' => 'Please enter valid email id!',
             'email.unique' => 'This email id already exists!',
             'username.required' => 'Please enter Username.',
             'username.unique' => 'This user name already exists!',
             'password.required' => 'Please enter a valid password!',
             'password.regex' => 'Password should have one uppercase, lowercase, special chars and integer',
             'policy.required' => 'Please select term and Conditions.',
             'sex.required' => 'Please select gender.'
         ];
     }
}
