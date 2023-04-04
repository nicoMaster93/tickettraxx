<?php

namespace App\Http\Requests\Ticket;

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
            'date_gen' => 'required',
            'number' => 'required',
            'vehicle' => 'required',
            'other_material' => 'required_if:material,other|unique:materials,name',
            'tonage' => 'required',
            'rate' => 'required',
            'total' => 'required',
        ];
    }
}
