@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-case-view">
            <div class="page-title">
                <ul class="pull-right">
                    @foreach($testSuites as $testSuite)
                        <li>Go To <a href="/laravel-test-suite/{{ $testSuite->slug }}">{{ $testSuite->full_name }}</a></li>
                    @endforeach
                </ul>
                <h1>Test case: <strong>{{ $testCase->full_name }}</strong></h1>
                <a href="/laravel-test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                &nbsp;
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print">Print</button>
            </div>

            <div class="test-case-description">
                {{ $testCase->description }}
            </div>

            <div class="options-box">
                <div class="options-box-row">
                    <div class="options-box-row-title">Info:</div>
                    <ul class="inline-options-list">
                        <li>ID: <strong>{{ $testCase->slug }}</strong></li>
                        <li>Published: <strong>{{ formatDate($testCase->published_at) }}</strong></li>
                        <li>Status: <span class="status status-{{ strtolower($testCase->status) }}">{{ $testCase->status }}</span></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Roles:</div>
                    <ul class="inline-options-list">
                        <li>Tester Role: <strong>{{ $testCase->tester_role }}</strong></li>
                        <li>Harness Role: <strong>{{ $testCase->harness_role }}</strong></li>
                        <li>Initiator: <strong>{{ $testCase->initiator }}</strong></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Properties:</div>
                    <ul class="inline-options-list">
                        <li>Conformance Levels:
                            @foreach($testCase->getUniqueConformanceLevels() as $conformanceLevel)
                                <strong>{{ $conformanceLevel }}</strong>
                            @endforeach
                        </li>
                        <li>Outcome Type: <strong>{{ $testCase->outcome_type }}</strong></li>
                        <li>Test Pattern: <span class="test-pattern-icon test-pattern-{{ $testCase->test_pattern }}" data-tooltip="tooltip" title="" data-original-title="{{ get_test_patterns_description($testCase->test_pattern) }}"></span></li>
                        <li>Execution mode: <strong>Auto</strong></li>
                        <li>Optional: <strong>{{ $testCase->is_optional ? 'Yes' : 'No' }}</strong></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Scenario:</div>
                    <ul class="inline-options-list">
                        <li><strong>{{ $testCase->scenario->testSuiteScenario->code }}</strong> ({{ $testCase->scenario->testSuiteScenario->description }})</li>
                    </ul>
                </div>
            </div>

            <div class="colored-box">
                <div class="colored-box-header">Test Steps</div>
                <div class="colored-box-content">
                    <div class="table-responsive">
                        <table class="table colored-table steps-table">
                        <thead>
                            <tr>
                                <th>Step</th>
                                <th>Action</th>
                                <th>Expected Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($testCase->steps as $testStep)
                                <tr class="step-row" id="step_anchor_{{ $testStep->step }}">
                                    <td class="col-sm-2 text-center">{{ $testStep->step }}</td>
                                    <td class="col-sm-5">{{ $testStep->action }}</td>
                                    <td class="col-sm-5">{{ $testStep->expected_result }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
@stop