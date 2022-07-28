<?php

namespace App\Http\Requests\User;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $data = [
            'name' => 'required|string|min:0|max:64',
            'email' => [
                'required',
                'string',
                'min:0',
                'max:64',
                Rule::unique('users', 'email')
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ],
            'role_type' => [
                'required',
                'integer',
                'between:1,3'
            ]
        ];

        if ($this->input('role_type') === User::ROLE_MANAGER) {
            $data['entities'] = [
                'nullable',
                'array'
            ];
        }
        return $data;
    }
}
