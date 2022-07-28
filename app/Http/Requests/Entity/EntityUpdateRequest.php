<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntityUpdateRequest extends FormRequest
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
            'servicePhone.regex' => 'Номер телефона должен быть в формате +7XXXXXXXXXX (+7 и 10 цифр).',
            'phone.regex' => 'Номер телефона должен быть в формате +7XXXXXXXXXX (+7 и 10 цифр).',
            'centralPostId.exists' => 'Такого поста не существует.',
            'id.between' => 'ID объекта должно иметь четырехзначный код.',
            'id.unique' => 'Такой ID уже занят',
            'dialingStatus.between' => 'Статус обзвона должен быть либо да, либо нет.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (count($this->input('customers')) >= 5) {
            throw new HttpException(400, "Должно быть не более 5 заказчиков");
        }

        if ($this->input('centralPostId')) {
            $validate = head(DB::select(
                "SELECT {$this->input('originalId')} <> 
                        (SELECT entity_id 
                            FROM guarded_objects 
                            WHERE id = {$this->input('centralPostId')} 
                        LIMIT 1) eq"
            ));

            if ($validate->eq === 1) {
                throw new HttpException(400, 'Данный пост прикреплен к другому объекту. 
                    Чтобы пост стал центральным, необходимо прикрепить к текущему объекту.');
            }
        }
        return [
            'name' => 'required|string|min:1|max:255',
            'customers' => 'array|nullable', // TODO доработать валидацию
            'phone' => 'required|string|min:|max:255|regex:/^\+7[0-9]{10}$/i',
            'customerName' => 'required|string|min:1|max:255',
            'comment' => 'nullable|string|max:10000',
            'address' => 'required|string|min:1|max:255',
            'servicePhone' => 'required|string|min:|max:255|regex:/^\+7[0-9]{10}$/i',
            'centralPostId' => [
                'nullable',
                'integer',
                Rule::exists('guarded_objects', 'id')
            ],
            'id' => [
                'required',
                'integer',
                'between:1000,9999',
                Rule::unique('entities', 'id')->ignore($this->input('id'), 'id')
            ],
            'originalId'=> 'integer',
            'callFrom' => 'nullable',
            'callTo' => 'nullable',
            'quantityCalls' => 'integer|min:0',
            'callBackQuantity' => 'integer|min:0',
            'maxDurationWork' => 'integer|min:0',
            'dialingStatus' => 'required|integer|between:0,1'
        ];
    }
}
