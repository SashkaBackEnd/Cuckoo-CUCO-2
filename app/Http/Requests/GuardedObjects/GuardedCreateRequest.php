<?php

namespace App\Http\Requests\GuardedObjects;


class GuardedCreateRequest extends GuardedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = parent::rules();
        $data['phone'] = 'required|string|unique:guarded_objects,phone,NULL,id,deleted_at,NULL|regex:/^\+7[0-9]{10}$/i';
        return $data;
    }
}
