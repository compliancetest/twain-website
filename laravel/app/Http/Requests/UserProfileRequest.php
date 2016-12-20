<?php

namespace App\Http\Requests;

class UserProfileRequest extends Request
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|phone_number',
            'first_page' => 'required',
            'timezone_settings' => 'required|timezone',
            'password' => 'required_with:specificationDocuments.description.*',
            'confirm_password' => 'required_with:password|same:password',
            'current_password' => 'required_with:password',
        ];
    }
}
