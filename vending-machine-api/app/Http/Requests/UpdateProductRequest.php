<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productName'     => ['sometimes', 'string', 'max:255'],
            'amountAvailable' => ['sometimes', 'integer', 'min:0'],
            'cost'            => ['sometimes', 'integer', 'min:5', function ($attr, $value, $fail) {
                if ($value % 5 !== 0) {
                    $fail('The cost must be a multiple of 5.');
                }
            }],
        ];
    }
}
