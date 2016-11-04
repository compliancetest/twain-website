@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content edit-test-case-page">
            <div class="page-title">
                <h1>Edit Test Case: {{ $testCase->full_name }}</h1>
            </div>

            <form action="#" method="post">

                {{-- Test Case Information --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseInformationBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Case Information</div>
                    <div class="colored-box-body collapse in" id="testCaseInformationBox">
                        <div class="colored-box-content">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="testCaseName">Name:</label>
                                        <input type="text" id="testCaseName" name="test_case_name" class="form-control" value="{{ $testCase->name }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="testCasePublishedDate">Published:</label>
                                        <div class="input-group">
                                            <input type="text" id="testCasePublishedDate" name="test_case_published_date" class="form-control datepicker-form-control" readonly value="{{ formatDate($testCase->published_at) }}" data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd">
                                            <span class="input-group-addon test-case-published-date"><span class="calendar-icon"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="testCaseStatus">Status:</label>
                                        <div class="radio-group status-radio-group">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="test_case_status" value="draft" @if($testCase->status == 'Draft') checked="checked"@endif>
                                                    <span class="status status-circle status-draft">D</span>Draft
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="test_case_status" value="active" @if($testCase->status == 'Active') checked="checked"@endif>
                                                    <span class="status status-circle status-active">A</span>Active
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="test_case_status" value="deprecated" @if($testCase->status == 'Deprecated') checked="checked"@endif>
                                                    <span class="status status-circle status-deprecated">C</span>Deprecated
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="test_case_status" value="obsolete" @if($testCase->status == 'Obsolete') checked="checked"@endif>
                                                    <span class="status status-circle status-obsolete">O</span>Obsolete
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="test_case_status" value="partial" @if($testCase->status == 'Partial') checked="checked"@endif>
                                                    <span class="status status-circle status-partial">P</span>Partial
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-1">
                                    <div class="form-group">
                                        <label for="testCaseVersion">Version:</label>
                                        <div class="manage-version-box row">
                                            <div class="manage-version-group">
                                                <span>Major</span>
                                                <input type="text" id="tcVersionMajor" name="version_major" class="form-control" readonly="readonly" value="{{ $testCase->version_major }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tcVersionMajor"></button>
                                            </div>
                                            <div class="manage-version-group">
                                                <span>Minor</span>
                                                <input type="text" id="tcVersionMinor" name="version_minor" class="form-control" readonly="readonly" value="{{ $testCase->version_minor }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tcVersionMinor"></button>
                                            </div>
                                            <div class="manage-version-group">
                                                <span>Patch</span>
                                                <input type="text" id="tcVersionPatch" name="version_patch" class="form-control" readonly="readonly" value="{{ $testCase->version_patch }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tcVersionPatch" disabled="disabled" data-tooltip="tooltip" title="Later version already exists."></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="testCaseDescription">Description:</label>
                                <textarea rows="5" class="form-control" id="testCaseDescription" name="test_case_description">{{ $testCase->description }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Choose Test Suite --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testTestSuitesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Test Suite</div>
                    <div class="colored-box-body collapse in" id="testTestSuitesBox">
                        <div class="colored-box-content">
                            <div class="checkboxes-group">
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_id[]" value="1" type="checkbox">
                                        Test Suite 1
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_id[]" value="2" type="checkbox">
                                        Test Suite 2
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_id[]" value="3" type="checkbox">
                                        Test Suite 3
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_id[]" value="4" type="checkbox">
                                        Test Suite 4
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_id[]" value="5" type="checkbox">
                                        Test Suite 5
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Choose Roles --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseRolesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Roles</div>
                    <div class="colored-box-body collapse in" id="testCaseRolesBox">
                        <div class="colored-box-content">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="testCaseTesterRole">Tester Role:</label>
                                        <select name="tester_role" id="testCaseTesterRole" class="form-control">
                                            <option value="">- Select -</option>
                                            <option value="Application" @if($testCase->tester_role == 'Application') selected="selected" @endif>Application</option>
                                            <option value="DataSource" @if($testCase->tester_role == 'DataSource') selected="selected" @endif>DataSource</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="testCaseHarnessRole">Harness Role:</label>
                                        <select name="harness_role" id="testCaseHarnessRole" class="form-control">
                                            <option value="">- Select -</option>
                                            <option value="Application" @if($testCase->harness_role == 'Application') selected="selected" @endif>Application</option>
                                            <option value="DataSource" @if($testCase->harness_role == 'DataSource') selected="selected" @endif>DataSource</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Initiator:</label>
                                        <div class="checkboxes-group initiator-role-group">
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="initiator" value="Tester"  @if($testCase->initiator == 'Tester') selected="selected" @endif>
                                                    Tester
                                                </label>
                                            </div>
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="initiator" value="Harness" @if($testCase->initiator == 'Harness') selected="selected" @endif>
                                                    Harness
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Choose Conformance Levels --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseConformanceLevelsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Conformance Levels</div>
                    <div class="colored-box-body collapse in" id="testCaseConformanceLevelsBox">
                        <div class="colored-box-content">
                            <h4 class="test-item-subheader">TWAIN v2.3 Compliance – Data Sources v1.0</h4>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="checkbox" name="conformance_level[]" value="Default" checked="checked">Default
                                    </label>
                                </dt>
                                <dd>All test cases created via this test suite are automatically associated with this conformance level. This association cannot be deleted.</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="checkbox" name="conformance_level[]" value="A">A
                                    </label>
                                </dt>
                                <dd>Conformance level A represents full compliance with all mandatory components of the TWAIN specification.</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Choose Scenarios --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseScenariosBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Scenarios</div>
                    <div class="colored-box-body collapse in" id="testCaseScenariosBox">
                        <div class="colored-box-content">
                            <h4 class="test-item-subheader">TWAIN v2.3 Compliance – Data Sources v1.0</h4>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="Default" checked="checked">Default
                                    </label>
                                </dt>
                                <dd>All test cases created via this test suite are initially associated with this scenario. In general, test cases will be associated with test suite specific scenarios, so it will usually be the case that no test cases are associated with this scenario.</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="SC" checked="checked">SC
                                    </label>
                                </dt>
                                <dd>Standard Capability. TWAIN Standard capabilities (ID's with a value less than 0x8000). Ignore Vendor Custom capabilities (ID’s with a value of 0x8000 or greater).</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="VC" checked="checked">VC
                                    </label>
                                </dt>
                                <dd>Vendor Specific Capability. Vendor Custom capabilities (ID's with a value of 0x8000 or greater). Ignore TWAIN Standard capabilities (ID’s with a value less than 0x8000).</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="SR" checked="checked">SR
                                    </label>
                                </dt>
                                <dd>Status Return Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="ST" checked="checked">ST
                                    </label>
                                </dt>
                                <dd>Stress Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="IT" checked="checked">IT
                                    </label>
                                </dt>
                                <dd>Non-UI Image Transfer Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="UI" checked="checked">UI
                                    </label>
                                </dt>
                                <dd>UI Driven Image Transfer Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="CX" checked="checked">CX
                                    </label>
                                </dt>
                                <dd>CAP_XFERCOUNT Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="VN" checked="checked">VN
                                    </label>
                                </dt>
                                <dd>Version Tests</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="radio" name="test-case-scenario" value="VV" checked="checked">VV
                                    </label>
                                </dt>
                                <dd>Verify Values For MSG_RESETALL and MSG_RESET</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Test Data --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseTestDataBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Data</div>
                    <div class="colored-box-body collapse in" id="testCaseTestDataBox">
                        <div class="colored-box-content dynamic-rows">
                            <h4 class="test-item-subheader">Test Samples</h4>
                            <div id="testCaseTestDataBoxContent">
                                <div class="form-group testCaseSampleRow">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Image:</label>
                                            <div class="upload-file-field">
                                                <input type="file" name="test_sample_file[]" class="input-file" />
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label for="testSampleFileDescription">Description:</label>
                                            <input type="text" name="test_sample_file_description[]" id="testSampleFileDescription" value="" class="form-control" />
                                        </div>
                                        <div class="col-md-1 action-col">
                                            <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testCaseSampleRow">Delete Sample Data</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="Page.helpers.addRow('#testCaseTestDataBoxTemplate','#testCaseTestDataBoxContent'); customizeFileTag();" class="btn btn-success btn-with-icon btn-add">Add image</button>

                            <br/><br/><br/>
                            <h4 class="test-item-subheader">Test Configuration</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="testCaseTestDataProfile">Attach JSON configuration profile file:</label>
                                    <select name="test_data_profile" id="testCaseTestDataProfile" class="form-control">
                                        <option value="">-- Select profile type -- </option>
                                        <option value="720">CX-01a_v1.0 TEFC v2.0</option>
                                        <option value="286">CX-01a_v1.0 TEFC v3.0</option>
                                        <option value="287">CX-01b_v1.0 TEFC v3.0</option>
                                        <option value="288">CX-01c_v1.0 TEFC v3.0</option>
                                        <option value="289">CX-02a_v1.0 TEFC v3.0</option>
                                        <option value="290">CX-02b_v1.0 TEFC v3.0</option>
                                        <option value="291">CX-02c_v1.0 TEFC v3.0</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                {{-- Test Execution --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseTestExecutionBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Execution</div>
                    <div class="colored-box-body collapse in" id="testCaseTestExecutionBox">
                        <div class="colored-box-content dynamic-rows">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="testCaseExecutionFile">Attach JSON test execution file:</label>
                                    <select name="test_data_profile" id="testCaseExecutionFile" class="form-control">
                                        <option value="">-- Select execution file -- </option>
                                        <option value="720">CX-01a_v1.0 TEFC v2.0</option>
                                        <option value="286">CX-01a_v1.0 TEFC v3.0</option>
                                        <option value="287">CX-01b_v1.0 TEFC v3.0</option>
                                        <option value="288">CX-01c_v1.0 TEFC v3.0</option>
                                        <option value="289">CX-02a_v1.0 TEFC v3.0</option>
                                        <option value="290">CX-02b_v1.0 TEFC v3.0</option>
                                        <option value="291">CX-02c_v1.0 TEFC v3.0</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                {{-- Choose Features --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseFeaturesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Features</div>
                    <div class="colored-box-body collapse in" id="testCaseFeaturesBox">
                        <div class="colored-box-content">
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="checkbox" name="test_case_feature[]" value="Feature 1" checked="checked">Feature 1
                                    </label>
                                </dt>
                                <dd>Feature 1 description</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="checkbox" name="test_case_feature[]" value="Feature 1" checked="checked">Feature 2
                                    </label>
                                </dt>
                                <dd>Feature 2 description</dd>
                            </dl>
                            <dl class="definition-list">
                                <dt>
                                    <label>
                                        <input type="checkbox" name="test_case_feature[]" value="Feature 1" checked="checked">Feature 3
                                    </label>
                                </dt>
                                <dd>Feature 3 description</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- Test Case Property --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCasePropertyBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Case Property</div>
                    <div class="colored-box-body collapse in" id="testCasePropertyBox">
                        <div class="colored-box-content">
                            <div class="form-group testSuiteRoleRow">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Outcome Type:</label>
                                        <div class="checkboxes-group">
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="outcome_type" value="Positive" @if($testCase->outcome_type == 'Positive') checked="checked" @endif>
                                                    Positive
                                                </label>
                                            </div>
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="outcome_type" value="Negative" @if($testCase->outcome_type == 'Negative') checked="checked" @endif>
                                                    Negative
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Optional:</label>
                                        <div class="checkboxes-group">
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="is_optional" value="Yes" @if($testCase->is_optional) checked="checked" @endif>
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="checkbox-inline">
                                                <label>
                                                    <input type="radio" name="is_optional" value="No" @if(!$testCase->is_optional) checked="checked" @endif>
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="testCaseTestPattern">Test Pattern:</label>
                                        <select name="test_case_pattern" id="testCaseTestPattern" class="form-control">
                                            <option value="1" @if($testCase->test_pattern == 1) selected="selected" @endif>Tester Initiated 1-Way Notification</option>
                                            <option value="2" @if($testCase->test_pattern == 2) selected="selected" @endif>Harness Initiated 1-Way Notification</option>
                                            <option value="3" @if($testCase->test_pattern == 3) selected="selected" @endif>Tester Initiated 2-Way Asynchronous Transaction</option>
                                            <option value="4" @if($testCase->test_pattern == 4) selected="selected" @endif>Harness Initiated 2-Way Asynchronous Transaction</option>
                                            <option value="5" @if($testCase->test_pattern == 5) selected="selected" @endif>Tester Initiated Synchronous Query/Response</option>
                                            <option value="6" @if($testCase->test_pattern == 6) selected="selected" @endif>Harness Initiated Synchronous Query/Response</option>
                                            <option value="7" @if($testCase->test_pattern == 7) selected="selected" @endif>Long Running Correlated Choreography</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Test Steps --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCaseStepsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Steps</div>
                    <div class="colored-box-body collapse in" id="testCaseStepsBox">
                        <div class="colored-box-content dynamic-rows">
                            <div id="testCaseStepsBoxContent">
                                <div class="form-group testCaseStepRow">
                                    @foreach($testCase->steps as $k => $step)
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Action:</label>
                                                <textarea name="steps[action][]" class="form-control" cols="30" rows="5">{{ $step->action }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Expected Result:</label>
                                                <textarea name="steps[expected_result][]" class="form-control" cols="30" rows="5">{{ $step->expected_result }}</textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <label>Step:</label>
                                                <input type="text" class="form-control" name="steps[step][]" value="{{ $step->step }}">
                                            </div>
                                            <div class="col-md-1 action-col">
                                                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testCaseStepRow">Delete Step</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" onclick="Page.helpers.addRow('#testCaseStepBoxTemplate','#testCaseStepsBoxContent'); customizeFileTag();" class="btn btn-success btn-with-icon btn-add">New Test Step</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="checkbox pull-right">
                        <label><input type="checkbox" value=""> Send Notification to members</label>
                    </div>
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save Test Case</button>
                    <a href="javascript:history.back();" class="btn btn-default btn-with-icon btn-cancel">Cancel</a>
                </div>

            </form>
        </div>
    </div>
@stop


@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('body').on('click', '.test-case-published-date', function () {
            $('#testCasePublishedDate').datepicker('show');
        });

        $('body').on('click', '[data-delete-row]', function () {
            var parenElement = '.' + $(this).data('delete-row');
            $(this).parents(parenElement).fadeOut('fast', function () {
                $(this).remove();
            });
            return false;
        });

        $('.manage-version-group .btn-add').click(function () {
            $('.manage-version-box').addClass('version-updated');
            var versionIdentifier = $(this).data('version-id');
            var inputToUpdate = $('#' + versionIdentifier);
            var currentVal = parseInt(inputToUpdate.val());
            if (!isNaN(currentVal)) {
                inputToUpdate.val(currentVal + 1);
            }
            inputToUpdate.after('<button type="button" class="version-cancel" data-tooltip="tooltip" data-trigger="hover" title="Undo"><span class="cancel-icon"></span></button>');
        });

        $('body').on('click', '.manage-version-group .version-cancel', function (e) {
            $('.manage-version-box').removeClass('version-updated');
            var currentVal = parseInt($(this).prev('.form-control').val());
            if (!isNaN(currentVal) && currentVal !== 0) {
                $(this).prev('input').val(currentVal - 1);
            }
            $('[data-tooltip]').tooltip('hide');
            $(this).remove();
        })


    });

</script>
<script type="text/html" id="testCaseTestDataBoxTemplate">
    <div class="form-group testCaseSampleRow">
        <div class="row">
            <div class="col-md-4">
                <label>Image:</label>
                <div class="upload-file-field">
                    <input type="file" name="test_sample_file[]" class="input-file" />
                </div>
            </div>
            <div class="col-md-7">
                <label for="testSampleFileDescription">Description:</label>
                <input type="text" name="test_sample_file_description[]" id="testSampleFileDescription" value="" class="form-control" />
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testCaseSampleRow">Delete Sample Data</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="testCaseStepBoxTemplate">
    <div class="form-group testCaseStepRow">
        <div class="row">
            <div class="col-md-5">
                <label>Action:</label>
                <textarea name="steps[action][]" class="form-control" cols="30" rows="5"></textarea>
            </div>
            <div class="col-md-5">
                <label>Expected Result:</label>
                <textarea name="steps[expected_result][]" class="form-control" cols="30" rows="5"></textarea>
            </div>
            <div class="col-md-1">
                <label>Step:</label>
                <input type="text" class="form-control" name="steps[step][]" value="">
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testCaseStepRow">Delete Step</button>
            </div>
        </div>
    </div>
</script>
@stop