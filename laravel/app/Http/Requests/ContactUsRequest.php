<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class ContactUsRequest extends Request
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
            'g-recaptcha-response' => 'required|recaptcha',
            'message_text' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'regex:/^([0-9\s\-\+\(\)]*)$/',
        ];
    }
}
