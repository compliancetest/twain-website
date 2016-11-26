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
        $rules = [
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
        ];
        if ($this->request->get('status') == 'Active') {
            $rules = array_merge($rules, [
                'profile_types' => 'required|array',

                'conformanceLevels.code' => 'required|array',
                'conformanceLevels.code.*' => 'required_with:conformanceLevels.description.*',
                'conformanceLevels.description.*' => 'required_with:conformanceLevels.code.*',

                'roles.name' => 'required|array',
                'roles.name.*' => 'required_with:roles.description.*',
                'roles.description.*' => 'required_with:roles.name.*',

                'features.name' => 'required_if:product_type,Application|array',
                'features.name.*' => 'required_with:features.description.*',
                'features.description.*' => 'required_with:features.name.*',

                'scenarios.code' => 'required|array',
                'scenarios.code.*' => 'required_with:scenarios.description.*|required_with:scenarios.sequence.*',
                'scenarios.description.*' => 'required_with:scenarios.code.*|required_with:scenarios.sequence.*',
                'scenarios.sequence.*' => 'required_with:scenarios.code.*|required_with:scenarios.description.*',

                'specificationDocuments.name.*' => 'required_with:specificationDocuments.description.*',
                'specificationDocuments.description.*' => 'required_with:specificationDocuments.name.*',
                'specificationDocuments.link.*' => 'url',
            ]);
        }
        return $rules;
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

            'conformanceLevels.*.required_with' => 'Please fill Conformance Level Code / Description',
            'roles.*.required_with' => 'Please fill Role Name / Description',
            'features.*.required_with' => 'Please fill Feature Name / Description',
            'features.*.required_if' => 'Please define at least one Feature',
            'scenarios.*.required_with' => 'Please fill Scenario Code / Description / Sequence number',
            'specificationDocuments.*.required_with' => 'Please fill Specification Document Name / Description / Location',
        ];
    }
}
