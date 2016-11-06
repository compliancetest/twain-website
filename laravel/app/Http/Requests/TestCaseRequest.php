<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TestCaseRequest extends Request
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
            'name' => 'required',
            'published_at' => 'date:Y-m-d',
            'status' => 'required|in:Draft,Obsolete,Active,Partial,Deprecated',
            'tester_role' => 'required|in:DataSource,Application',
            'harness_role' => 'required|in:DataSource,Application',
            'initiator' => 'required|in:Tester,Harness',
            'outcome_type' => 'required|in:Positive,Negative',
            'is_optional' => 'required',
            'description' => 'required|string',

            'test_suite_id' => 'required|array',

            'conformanceLevel.*' => 'required|array',
            'scenario.*' => 'required|array',
            'features.*' => 'required|array',

            'steps.action.*' => 'required_with:steps.expected_result.*,required_with:steps.step.*',
            'steps.expected_result.*' => 'required_with:steps.action.*,required_with:steps.step.*',
            'steps.step.*' => 'integer,required_with:steps.expected_result.*,required_with:steps.action.*',
        ];
    }
}
