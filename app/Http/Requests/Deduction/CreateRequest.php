<?php

namespace App\Http\Requests\Deduction;

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
        $rules = ['deduction_type' => 'required'];

        if($this->has('deduction_type') && $this->input("deduction_type")=="1"){
            $rules['date_loan'] = 'required';
            $rules['total_value'] = 'required';
            $rules['balance_due'] = 'required';
            $rules['charge_type'] = 'required';
            if($this->has('charge_type') && $this->input("charge_type")=="number_installments"){
                $rules['number_installments'] = 'required';               
            }
             
            if($this->has('charge_type') && $this->input("charge_type")=="fixed_value"){
                $rules['fixed_value'] = 'required';
            }
            $rules['days'] = 'required';
            $rules['contractor'] = 'required';
        }

        if($this->has('deduction_type') && $this->input("deduction_type")=="2"){
            $rules['vehicles'] = 'required';
            if($this->has('vehicles')){
                for ($i=1; $i <= $this->input("vehicles"); $i++) { 
                    $rules['vehicle_'.$i] = 'required';
                    $rules['date_vehicle_'.$i] = 'required';

                    $rules['city_'.$i] = 'required';
                    $rules['state_'.$i] = 'required';
                    $rules['gallons_'.$i] = 'required';
                    $rules['total_'.$i] = 'required';
                }
            }
        }
        if($this->has('deduction_type') && $this->input("deduction_type")=="3"){
            $rules['date_loan'] = 'required';
            $rules['contractor'] = 'required';
        }
        return $rules;
    }
}
