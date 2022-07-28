<?php

namespace App\Http\Requests\GuardedObjects;

use Illuminate\Validation\Rule;

class GuardedUpdateRequest extends GuardedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = parent::rules();
        $guardedObject = $this->route('guardedObject');
        $data['phone'] = [
            'required',
            'string',
            Rule::unique('guarded_objects', 'phone')->ignore($guardedObject->id)->whereNull('deleted_at'),
            'regex:/^\+7[0-9]{10}$/i'
        ];
        return $data;
    }
}
