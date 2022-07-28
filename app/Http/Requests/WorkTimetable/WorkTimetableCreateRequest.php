<?php

namespace App\Http\Requests\WorkTimetable;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkTimetableCreateRequest extends FormRequest
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
            'array' => 'Поле :attribute должно быть массивом',
            'guardedObjectId.exists' => 'Передан несуществующий ID поста.'
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
            'Mon' => [
                'required',
                'array',
                'Mon.salary' => 'required',
                'Mon.times' => 'nullable|array',
                'Mon.times.*.from' => 'required|integer',
                'Mon.times.*.to' => 'required|integer'
            ],
            'Tue' => [
                'required',
                'Tue.salary' => 'required',
                'Tue.times' => 'nullable|array',
                'Tue.times.*.from' => 'required|integer',
                'Tue.times.*.to' => 'required|integer'
            ],
            'Wed' => [
                'required',
                'array',
                'Wed.salary' => 'required',
                'Wed.times' => 'nullable|array',
                'Wed.times.*.from' => 'required|integer',
                'Wed.times.*.to' => 'required|integer'
            ],
            'Thu' => [
                'required',
                'array',
                'Thu.salary' => 'required',
                'Thu.times' => 'nullable|array',
                'Thu.times.*.from' => 'required|integer',
                'Thu.times.*.to' => 'required|integer'
            ],
            'Fri' => [
                'required',
                'array',
                'Fri.salary' => 'required',
                'Fri.times' => 'nullable|array',
                'Fri.times.*.from' => 'required|integer',
                'Fri.times.*.to' => 'required|integer'
            ],
            'Sat' => [
                'required',
                'array',
                'Sat.salary' => 'required',
                'Sat.times' => 'nullable|array',
                'Sat.times.*.from' => 'required|integer',
                'Sat.times.*.to' => 'required|integer'
            ],
            'Sun' => [
                'required',
                'array',
                'Sun.salary' => 'required',
                'Sun.times' => 'nullable|array',
                'Sun.times.*.from' => 'required|integer',
                'Sun.times.*.to' => 'required|integer'
            ]
        ];
    }
}
