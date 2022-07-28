<?php

namespace App\Http\Requests\GuardedObjects;

use Illuminate\Foundation\Http\FormRequest;

abstract class GuardedRequest extends FormRequest
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

    public function messages(): array
    {
        return [
            'string' => 'Поле :attribute должно быть строкой.',
            'min' => 'Поле :attribute не должно быть меньше :min символов.',
            'max' => 'Поле :attribute не должно быть больше :max символов.',
            'integer' => 'Поле :attribute должно быть числовым.',
            'required' => 'Поле :attribute обязательно для заполнения.',
            'phone.regex' => 'Номер телефона должен быть в формате +7XXXXXXXXXX (+7 и 10 цифр).',
            'array' => 'Поле :attribute должно быть массивом',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'Mon' => [
                'required',
                'array'
            ],
            'Tue' => [
                'required',
                'array'
            ],
            'Wed' => [
                'required',
                'array'
            ],
            'Thu' => [
                'required',
                'array'
            ],
            'Fri' => [
                'required',
                'array'
            ],
            'Sat' => [
                'required',
                'array'
            ],
            'Sun' => [
                'required',
                'array'
            ],
            '*.salary' => 'nullable',
            '*.times' => ['nullable', 'array'],
            '*.times.*.from' => ['required', 'integer'],
            '*.times.*.to' => ['required', 'integer']
        ];
    }
}
