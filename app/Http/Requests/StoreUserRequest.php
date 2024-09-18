<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            //'phone_number' => 'required|string|max:255',
            //'address' => 'required|string|max:255',
            //'region' => 'required|string|max:255',
            //'city' => 'required|string|max:255',
            //'country' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ];
    }
}
