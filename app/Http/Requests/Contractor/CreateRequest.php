<?php

namespace App\Http\Requests\Contractor;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'state' => 'required',
            'other_state' => 'required_if:state,other|unique:location,location_name',
            'city' => 'required',
            'other_city' => 'required_if:city,other|unique:location,location_name',
            'address' => 'required',
            'id_type' => 'required',            
            'company_name' => 'required',
            'company_telephone' => 'required',
            'percentage' => 'required',
            'email' => 'required|email|unique:users,email',
            'zip_code' => 'required|integer|digits:5'
        ];
        if($this->has('id_type') && $this->input("id_type")=="2"){
            $rules['id'] = 'required|digits:9';
        }
        else{
            $rules['id'] = 'required';
        }
        return $rules;
    }
}
