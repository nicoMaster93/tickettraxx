<?php

namespace App\Http\Requests\Contractor;

use Illuminate\Foundation\Http\FormRequest;

class EditQuickBooksRequest extends FormRequest
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
            'id_customer' => 'required',
            'id_quickbooks' => 'required|unique:contractors,id_quickbooks',
        ];
        return $rules;
    }
}
