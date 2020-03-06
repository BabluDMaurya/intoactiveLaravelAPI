<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NutritionRequest extends FormRequest
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
            'nutriTitle'    => 'required',
            'nutriDescription' => 'required',
            'nutriMealType' => 'required',            
            'nutriServeSize'   => 'required',
            'nutriPrepHrs'      => 'required',
            'nutriPrepMin'      => 'required',
            
            
        ];
    }

      public function messages()
     {
         return [
             'nutriTitle.required' => 'Please enter Title',
             'nutriDescription.required' => 'Please enter Description.',
             'nutriMealType.required' => 'Please enter Meal Type',
             'nutriServeSize.required' => 'Please enter a ServeSize!',
             'nutriPrepHrs.required' => 'please select Prepration Hrs',
             'nutriPrepMin.required' => 'please select Prepration Minites.',
            
         ];
     }
}
