<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productName'     => ['required', 'string', 'max:255'],
            'amountAvailable' => ['required', 'integer', 'min:0'],
            'cost'            => ['required', 'integer', 'min:5', function ($attr, $value, $fail) {
                if ($value % 5 !== 0) {
                    $fail('The cost must be a multiple of 5.');
                }
            }],
        ];
    }
}
