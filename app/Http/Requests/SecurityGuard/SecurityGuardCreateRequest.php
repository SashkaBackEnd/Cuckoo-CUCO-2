<?php

namespace App\Http\Requests\SecurityGuard;

use Illuminate\Foundation\Http\FormRequest;

class SecurityGuardCreateRequest extends FormRequest
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

    public function messages()
    {
        //todo добавить текст по остальным форматам
        return [
            'phone.regex' => 'Номер телефона должен быть в формате +7XXXXXXXXXX (+7 и 10 цифр)',
            'integer' => 'Неверное значение поля :attribute',
            'required' => 'Поле :attribute обязательно для заполнения',
            'date' => 'Неверный формат даты',
            'max' => 'Поле :attribute должно содержать не более :max символов',
            'min' => 'Поле :attribute должно содержать не менее :min символов',
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
            'surname' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'patronymic' => 'min:0|max:32',
            'birthDate' => 'required|integer',
            'phone' => ['required', 'string', 'regex:/^\+7[0-9]{10}$/i'],
            'license' => 'required|integer|min:0|max:1',
            'comment' => 'nullable|string|max:4000',
            'licenseRank' => 'nullable|integer|between:1,9',
            'knewAboutUs' => 'nullable|string|max:1024',
            'leftThings' => 'nullable|string|max:1024',
            'drivingLicense' => 'required|integer|min:0|max:1',
            'car' => 'nullable|string|max:255',
            'medicalBook' => 'nullable|string|max:512',
            'gun' => 'nullable|string|max:255',
            'debts' => 'nullable|string|max:1024',
            'workType' => ['nullable', 'string', 'regex:/(^смены|^вахта)/i'], // todo Определить как константы
            'status' => ['required' , 'string', 'regex:/(^обычный|^служебный)/i'], // todo Определить как константы
            'licenseToDate' => 'nullable|integer'
        ];
    }
}
