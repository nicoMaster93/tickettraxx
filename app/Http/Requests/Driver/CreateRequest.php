<?php

namespace App\Http\Requests\Driver;

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
        return [
            'name' => 'required',
            'state' => 'required',
            'other_state' => 'required_if:state,other|unique:location,location_name',
            'city' => 'required',
            'other_city' => 'required_if:city,other|unique:location,location_name',
            'address' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ];
    }
}
