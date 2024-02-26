<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            //
            'number' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    $pattern = '/\d+\.\d+/';
                    if (!preg_match($pattern, $value, $matches)) {
                        return $fail('The ' . $attribute . ' not shopee product url');
                    }
                }
            ],
        ];
    }
}
