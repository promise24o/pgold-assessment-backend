<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'exists:rates,code'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.exists' => 'The selected asset is not available.',
            'amount.min' => 'Amount must be greater than zero.',
        ];
    }
}
