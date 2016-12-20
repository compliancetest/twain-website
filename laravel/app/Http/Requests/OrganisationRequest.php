<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class OrganisationRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return is_organisation_admin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'organisation_name' => 'required',
            'organisation_description' => 'required',
            'organisation_website' => 'url',
            'secondary_contact_email' => 'email',
            'contact_email' => 'email',
        ];
    }
}
