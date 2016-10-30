@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-case-view">
            <div class="page-title">
                <ul class="pull-right">
                    <li>Go To <a href="#">Scanning Operations – Data Sources v1.0</a></li>
                    <li>Go To <a href="#">Scanning Operations – Data Sources v2.0</a></li>
                </ul>
                <h1>Test case: <strong>SD-02e v1.0</strong></h1>
                <a href="/laravel-test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                &nbsp;
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print">Print</button>
            </div>

            <div class="test-case-description">
                Blank page detection and handling. Set ICAP_AUTODISCARDBLANKPAGES to TWBP_DISABLE.
            </div>

            <div class="options-box">
                <div class="options-box-row">
                    <div class="options-box-row-title">Info:</div>
                    <ul class="inline-options-list">
                        <li>ID: <strong>SD-02e_V1.0</strong></li>
                        <li>Published: <strong>2016-02-23 </strong></li>
                        <li>Status: <span class="status status-active">Active</span></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Roles:</div>
                    <ul class="inline-options-list">
                        <li>Tester Role: <strong>DataSource</strong></li>
                        <li>Harness Role: <strong>Application</strong></li>
                        <li>Initiator: <strong>Harness</strong></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Properties:</div>
                    <ul class="inline-options-list">
                        <li>Conformance Levels: <strong>A</strong></li>
                        <li>Outcome Type: <strong>Positive</strong></li>
                        <li>Test Pattern: <span class="test-pattern-icon test-pattern-1" data-tooltip="tooltip" title="" data-original-title="The Tester Initiated 1-Way Notification pattern represents the case where a tester sends a single message (eg a initiate.rollover.request) to the the harness, which performs a set of validations and then stores the result for the tester to view via the message log. There is no correlated response message."></span></li>
                        <li>Execution mode: <strong>Auto</strong></li>
                        <li>Optional: <strong>No</strong></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Scenario:</div>
                    <ul class="inline-options-list">
                        <li><strong>SD</strong> (Status Detection)</li>
                        <li><strong>ST</strong> (Stress Tests)</li>
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
                                <th>Steps</th>
                                <th class="text-left">Action</th>
                                <th class="text-left">Expected Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="step-row" id="step_anchor_1">
                                <td class="col-sm-2 text-center">1</td>
                                <td class="col-sm-5">Load and select the data source</td>
                                <td class="col-sm-5">The data source has been loaded and opened - state 4.</td>
                            </tr>
                            <tr class="step-row" id="step_anchor_2">
                                <td class="col-sm-2 text-center">2</td>
                                <td class="col-sm-5">Receive DG_CONTROL / DAT_CAPABILITY / MSG_SET with ICAP_AUTODISCARDBLANKPAGES set to to TWBP_DISABLE. All images should be delivered to the application, none of them should be discarded.</td>
                                <td class="col-sm-5">Response code is TWRC_SUCCESS.<br/>If not, skip the test.</td>
                            </tr>
                            <tr class="step-row" id="step_anchor_3">
                                <td class="col-sm-2 text-center">3</td>
                                <td class="col-sm-5">Scan a blank page.</td>
                                <td class="col-sm-5">A image of the blank page is transferred to the application.</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>

                    {{--<div class="steps">--}}
                        {{--<div class="steps-header">--}}
                            {{--<div class="col-sm-2 text-center">Steps</div>--}}
                            {{--<div class="col-sm-5">Action</div>--}}
                            {{--<div class="col-sm-5">Expected Result</div>--}}
                        {{--</div>--}}
                        {{--<ul class="steps-list">--}}
                            {{--<li id="step_anchor_1">--}}
                                {{--<div class="col-sm-2 text-center">1</div>--}}
                                {{--<div class="col-sm-5">Load and select the data source</div>--}}
                                {{--<div class="col-sm-5">The data source has been loaded and opened - state 4.</div>--}}
                            {{--</li>--}}
                            {{--<li id="step_anchor_2">--}}
                                {{--<div class="col-sm-2 text-center">2</div>--}}
                                {{--<div class="col-sm-5">Receive DG_CONTROL / DAT_CAPABILITY / MSG_SET with ICAP_AUTODISCARDBLANKPAGES set to to TWBP_DISABLE. All images should be delivered to the application, none of them should be discarded.</div>--}}
                                {{--<div class="col-sm-5">Response code is TWRC_SUCCESS.<br/>If not, skip the test.</div>--}}
                            {{--</li>--}}
                            {{--<li id="step_anchor_3">--}}
                                {{--<div class="col-sm-2 text-center">3</div>--}}
                                {{--<div class="col-sm-5">Scan a blank page.</div>--}}
                                {{--<div class="col-sm-5">A image of the blank page is transferred to the application.</div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                </div>

            </div>

        </div>
    </div>
@stop