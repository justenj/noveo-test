<?php

namespace App\Http\Requests\Users;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        return [
            'email' => [
                'required',
                'unique:users,email',
                'email'
            ],
            'last_name' => 'required',
            'first_name' => 'required',
            'state' => [
                Rule::in(User::states())
            ],
            'groups' => [
                'required',
                'array',
                'exists:groups,id',
            ]
        ];
    }
}
