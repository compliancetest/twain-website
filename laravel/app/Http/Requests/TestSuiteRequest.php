<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TestSuiteRequest extends Request
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
            'community_id' => 'required|exists:communities,id',
            'test_suite_type' => 'required|array|in:Data Exchange,Environment,Quality,Some Other Type,Web Technology',
            'name' => 'required|string',
            'short_name' => 'required|string',
            'published_at' => 'date:Y-m-d',
            'issuer' => 'required',
            'revision_description' => 'string',
            'status' => 'required|in:Draft,Obsolete,Active,Partial,Deprecated',
            'product_type' => 'required|in:DataSource,Application',
            'description' => 'required|string',

            'conformanceLevels.code' => 'required|array',
            'conformanceLevels.description.*' => 'required_with:conformanceLevels.code.*',

            'roles.name' => 'required|array',
            'roles.description.*' => 'required_with:roles.name.*',

            'features.name' => 'required_if:product_type,Application|array',
            'features.description.*' => 'required_with:features.name.*',

            'scenarios.code' => 'required|array',
            'scenarios.description.*' => 'required_with:scenarios.code.*',
            'scenarios.sequence.*' => 'required_with:scenarios.code.*',

//            'specificationDocuments.name' => 'required|array',
            'specificationDocuments.description.*' => 'required_with:specificationDocuments.name.*',
            'specificationDocuments.link.*' => 'url',
            'specificationDocuments.file.*' => 'file|required_with:specificationDocuments.name.*|required_without:specificationDocuments.link.*',

            'roles.name' => 'required|array',
            'roles.name.*' => 'string|min:3',
            'roles.description.*' => 'required_with:roles.name.*',


        ];
    }

    public function messages()
    {
        return [
            'conformanceLevels.code.required' => 'Please define at least one Conformance Level',
            'roles.name.required' => 'Please define at least one Role',
            'features.name.required' => 'Please define at least one Feature',
            'scenarios.code.required' => 'Please define at least one Scenario',
            'scenarios.code.required' => 'Please define at least one Scenario',
            'name.required' => 'Test Suite title is required',
            'short_name.required' => 'Test Suite name is required',
        ];
    }
}
