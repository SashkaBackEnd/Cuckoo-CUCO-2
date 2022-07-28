<?php

namespace App\Http\Requests\WorkTimetableDates;

use Illuminate\Foundation\Http\FormRequest;

class WorkTimetableDateCreateRequest extends FormRequest
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
            'integer' => 'Поле :attribute должно быть числовым.',
            'required' => 'Поле :attribute обязательно для заполнения.',
            'array' => 'Поле :attribute должно быть массивом'
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
            'day' => 'required|integer',
            'salary' => 'required',
            'times' => [
                'required|array',
                'times.*.to' => 'required|integer',
                'times.*.from' => 'required|integer'
            ]
        ];
    }
}
