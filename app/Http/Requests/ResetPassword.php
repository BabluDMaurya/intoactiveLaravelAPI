<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPassword extends FormRequest
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
            'cur_pass'    => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'new_pass' => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
            'cnf_pass' => 'required|string|min:8|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',  
        ];
    }
    
     public function messages()
     {
         return [             
             'cur_pass.required' => 'Please enter a valid current password!',
             'curr_pass.length' => 'Current Password should have one uppercase, lowercase, special chars and integer',
             'curr_pass.regex' => 'Current Password should have one uppercase, lowercase, special chars and integer',
             'cnf_pass.required' => 'Please enter a valid new  password!',
             'cnf_pass.length' => 'Password should have one uppercase, lowercase, special chars and integer',
             'cnf_pass.regex' => 'Password should have one uppercase, lowercase, special chars and integer',
         ];
     }
}
