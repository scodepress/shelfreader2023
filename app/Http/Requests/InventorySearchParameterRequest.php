<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventorySearchParameterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
		'beginningBarcode' => 'nullable|digits:12|numeric|starts_with:0',
		'endingBarcode' => 'nullable|digits:12|numeric|starts_with:0',
		'beginningDate' => 'date|nullable',
		'endingDate' => 'date|nullable',
        ];
    }
}
