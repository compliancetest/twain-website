@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content edit-test-suite-page">
            <div class="page-title">
                <h1>Edit Test Suite: Name</h1>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="colored-box collapsible-box">
                        <div class="colored-box-header"><a href="#chooseCommunityBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Community</div>
                        <div class="colored-box-body collapse in" id="chooseCommunityBox">
                            <div class="colored-box-content">
                                <div class="form-group">
                                    <select name="community_id" id="communityId" class="form-control">
                                        <option value="2b631ece-4fa1-4277-96bf-6efe1a279893">Find</option>
                                        <option value="857d8b15-76bd-49d4-9e4b-b2d35aea7db1">TWAIN</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="colored-box collapsible-box">
                        <div class="colored-box-header"><a href="#testSuiteTypesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Suite Types</div>
                        <div class="colored-box-body collapse in" id="testSuiteTypesBox">
                            <div class="colored-box-content test-suite-types-box">
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_type[]" value="11" type="checkbox">
                                        Data Exchange
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_type[]" value="12" checked="checked" type="checkbox">
                                        Environment
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_type[]" value="13" checked="checked" type="checkbox">
                                        Quality
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_type[]" value="14" type="checkbox">
                                        Some Other Type
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_type[]" value="15" type="checkbox">
                                        Web Technology
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#testSuiteInformationBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Suite Information</div>
                <div class="colored-box-body collapse in" id="testSuiteInformationBox">
                    <div class="colored-box-content">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testSuiteTitle">Title:</label>
                                    <input type="text" id="testSuiteTitle" name="test_suite_title" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testSuiteName">Name:</label>
                                    <input type="text" id="testSuiteName" name="test_suite_name" class="form-control" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="testSuitePublishedDate">Published:</label>
                                    <div class="input-group">
                                        <input type="text" id="testSuitePublishedDate" name="test_suite_published_date" class="form-control datepicker-form-control" readonly value="" data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd">
                                        <span class="input-group-addon test-suite-published-date"><span class="calendar-icon"></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="testSuiteIssuer">Issuer:</label>
                                    <input type="text" id="testSuiteIssuer" name="test_suite_issuer" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="testSuiteRevisionDescription">Revision Description:</label>
                                    <input type="text" id="testSuiteRevisionDescription" name="test_suite_revision_description" class="form-control" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="testSuiteStatus">Status:</label>
                                    <div class="radio-group status-radio-group">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="test_suite_status" value="draft">
                                                <span class="circle-status status-draft">Draft</span>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="test_suite_status" value="active">
                                                <span class="circle-status status-active">Active</span>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="test_suite_status" value="deprecated">
                                                <span class="circle-status status-deprecated">Deprecated</span>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="test_suite_status" value="obsolete">
                                                <span class="circle-status status-obsolete">Obsolete</span>
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="test_suite_status" value="partial">
                                                <span class="circle-status status-partial">Partial</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-md-offset-2">
                                <div class="form-group">
                                    <label for="testSuiteVersion">Version:</label>

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Type:</label>
                            <div class="radio-group">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="test_suite_product_type" value="DataSource">
                                        DataSource
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="test_suite_product_type" value="Application">
                                        Application
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="testSuiteDescription">Description:</label>
                            <textarea rows="5" class="form-control" id="testSuiteDescription" name="test_suite_description"></textarea>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#conformanceLevelsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Conformance Levels</div>
                <div class="colored-box-body collapse in" id="conformanceLevelsBox">
                    <div class="colored-box-content dynamic-rows" id="conformanceLevelContent">


                        <div class="form-group conformance-level-row">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Conformance Level Code:</label>
                                    <input type="text" class="form-control" name="lvl_code[]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Conformance Level Description:</label>
                                    <textarea rows="3" class="form-control" name="lvl_desc[]"></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="form-group conformance-level-row">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Conformance Level Code:</label>
                                    <input type="text" class="form-control" name="lvl_code[]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Conformance Level Description:</label>
                                    <textarea rows="3" class="form-control" name="lvl_desc[]"></textarea>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="conformance-level-row">Delete Conformance Level</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#conformanceLevelTemplate','#conformanceLevelContent');" class="btn btn-success btn-with-icon btn-add">New Conformance Level</button>
                    </div>
                </div>
            </div>

            {{-- Features List --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#featuresListBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Features List</div>
                <div class="colored-box-body collapse in" id="featuresListBox">
                    <div class="colored-box-content dynamic-rows" id="featuresListContent">

                        <div class="form-group featureRow">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Feature Name:</label>
                                    <input type="text" class="form-control" name="feature_name[]]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Description:</label>
                                    <textarea rows="3" class="form-control" name="feature_description[]"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group featureRow">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Feature Name:</label>
                                    <input type="text" class="form-control" name="feature_name[]]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Description:</label>
                                    <textarea rows="3" class="form-control" name="feature_description[]"></textarea>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="featureRow">Delete Feature</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#featureTemplate','#featuresListContent');" class="btn btn-success btn-with-icon btn-add">New Feature</button>
                    </div>
                </div>
            </div>

            {{-- Scenarios --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#scenariosBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Scenarios</div>
                <div class="colored-box-body collapse in" id="scenariosBox">
                    <div class="colored-box-content dynamic-rows" id="scenariosContent">

                        <div class="form-group scenarioRow">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Code:</label>
                                    <input type="text" class="form-control" name="scenario_code[]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Description:</label>
                                    <textarea rows="3" class="form-control" name="scenario_desc[]"></textarea>
                                </div>
                                <div class="col-md-1">
                                    <label>Sequence:</label>
                                    <input type="text" class="form-control" name="scenario_sequence[]" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group scenarioRow">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Code:</label>
                                    <input type="text" class="form-control" name="scenario_code[]" value=""/>
                                </div>
                                <div class="col-md-6">
                                    <label>Description:</label>
                                    <textarea rows="3" class="form-control" name="scenario_desc[]"></textarea>
                                </div>
                                <div class="col-md-1">
                                    <label>Sequence:</label>
                                    <input type="text" class="form-control" name="scenario_sequence[]" value=""/>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="scenarioRow">Delete Scenario</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#scenarioTemplate','#scenariosContent');" class="btn btn-success btn-with-icon btn-add">New Scenario</button>
                    </div>
                </div>
            </div>

            {{-- Test Data --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#testDataBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Data</div>
                <div class="colored-box-body collapse in" id="testDataBox">
                    <div class="colored-box-content dynamic-rows">
                        <div id="messageTemplatesContent">
                            <h4 class="test-suite-subheader">Message Templates</h4>
                            <div class="form-group messageTemplatesRow">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Template Name:</label>
                                        <input type="text" class="form-control" name="message_template_name[]" value=""/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Template URI:</label>
                                        <select name="message_template_url[]" class="form-control">
                                            <option>Select a Template</option>
                                            <option>Template 1</option>
                                            <option>Template 2</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 action-col">
                                        <button class="btn btn-primary btn-icon btn-delete" data-delete-row="messageTemplatesRow">Delete Message Template</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="Page.helpers.addRow('#messageTemplatesTemplate','#messageTemplatesContent');" class="btn btn-success btn-with-icon btn-add">New Template</button>

                        <div class="test-data-profile-types">
                            <h4 class="test-suite-subheader">Profile Type:</h4>
                            <div class="checkboxes-group clearfix">
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 1</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 2</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 3</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 4</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 5</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 6</a>
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="test_suite_profile_type" type="checkbox">
                                        <a href="javascript:void(0);">Profile Type 7</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Test Suites --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#relatedTestSuitesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Related Test Suites</div>
                <div class="colored-box-body collapse in" id="relatedTestSuitesBox">
                    <div class="colored-box-content dynamic-rows" id="relatedTestSuitesContent">

                        <div class="form-group relatedTestSuiteRow">
                            <div class="row">
                                <div class="col-md-5">
                                    <label>Related Suite:</label>
                                    <select class="form-control" name="related_ts[]">
                                        <option>Select test suite</option>
                                        <option>Test suite 1</option>
                                        <option>Test suite 2</option>
                                        <option>Test suite 3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Description:</label>
                                    <textarea rows="3" class="form-control" name="related_ts_desc[]"></textarea>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="relatedTestSuiteRow">Delete Related Test Suite</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#relatedTestSuiteTemplate','#relatedTestSuitesContent');" class="btn btn-success btn-with-icon btn-add">New Test Suite</button>
                    </div>
                </div>
            </div>

            {{-- Roles --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#testSuitesRolesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Roles</div>
                <div class="colored-box-body collapse in" id="testSuitesRolesBox">
                    <div class="colored-box-content dynamic-rows" id="testSuitesRolesContent">

                        <div class="form-group testSuiteRoleRow">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Name:</label>
                                    <input type="text" class="form-control" name="role_names[]" value=""/>
                                </div>
                                <div class="col-md-5">
                                    <label>Description:</label>
                                    <input type="text" class="form-control" name="role_descs[]" value=""/>
                                </div>
                                <div class="col-md-3">
                                    <label>Profile types:</label>
                                    <input type="text" class="form-control" name="role_types[]" value=""/>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testSuiteRoleRow">Delete Test Suite Role</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#testSuiteRoleTemplate','#testSuitesRolesContent');" class="btn btn-success btn-with-icon btn-add">New Role</button>
                    </div>
                </div>
            </div>

            {{-- Specification Documents --}}
            <div class="colored-box collapsible-box">
                <div class="colored-box-header"><a href="#specificationDocsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Specification Documents</div>
                <div class="colored-box-body collapse in" id="specificationDocsBox">
                    <div class="colored-box-content dynamic-rows" id="specificationDocsBoxContent">

                        <div class="form-group specificationDocRow">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Document Name:</label>
                                    <input type="text" class="form-control" name="doc_name[]" value=""/>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Document Description:</label>
                                        <textarea name="doc_desc[]" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Document Location:</label>
                                        <input type="text" class="form-control" name="doc_loc[]" value=""/>
                                    </div>
                                    <div class="form-group">
                                        <div class="upload-file-field">
                                            <input type="file" name="doc_file[]" class="input-file" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 action-col">
                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="specificationDocRow">Delete Document</button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="colored-box-footer">
                        <button type="button" onclick="Page.helpers.addRow('#specificationDocsBoxTemplate','#specificationDocsBoxContent'); customizeFileTag();" class="btn btn-success btn-with-icon btn-add">New Document</button>
                    </div>
                </div>
            </div>




        </div>
    </div>
@stop

@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('body').on('click', '.test-suite-published-date', function () {
            $('#testSuitePublishedDate').datepicker('show');
        });

        $('body').on('click', '[data-delete-row]', function () {
            var parenElement = '.' + $(this).data('delete-row');
            $(this).parents(parenElement).fadeOut('fast', function () {
                $(this).remove();
            });
            return false;
        })


    });

</script>
<script type="text/html" id="conformanceLevelTemplate">
    <div class="form-group conformance-level-row">
        <div class="row">
            <div class="col-md-5">
                <label>Conformance Level Code:</label>
                <input type="text" class="form-control" name="lvl_code[]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Conformance Level Description:</label>
                <textarea rows="3" class="form-control" name="lvl_desc[]"></textarea>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="conformance-level-row">Delete Conformance Level</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="featureTemplate">
    <div class="form-group featureRow">
        <div class="row">
            <div class="col-md-5">
                <label>Feature Name:</label>
                <input type="text" class="form-control" name="feature_name[]]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="feature_description[]"></textarea>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="featureRow">Delete Feature</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="scenarioTemplate">
    <div class="form-group scenarioRow">
        <div class="row">
            <div class="col-md-4">
                <label>Code:</label>
                <input type="text" class="form-control" name="scenario_code[]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="scenario_desc[]"></textarea>
            </div>
            <div class="col-md-1">
                <label>Sequence:</label>
                <input type="text" class="form-control" name="scenario_sequence[]" value=""/>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="scenarioRow">Delete Scenario</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="messageTemplatesTemplate">
    <div class="form-group messageTemplatesRow">
        <div class="row">
            <div class="col-md-5">
                <label>Template Name:</label>
                <input type="text" class="form-control" name="message_template_name[]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Template URI:</label>
                <select name="message_template_url[]" class="form-control">
                    <option>Select a Template</option>
                    <option>Template 1</option>
                    <option>Template 2</option>
                </select>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="messageTemplatesRow">Delete Message Template</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="relatedTestSuiteTemplate">
    <div class="form-group relatedTestSuiteRow">
        <div class="row">
            <div class="col-md-5">
                <label>Related Suite:</label>
                <select class="form-control" name="related_ts[]">
                    <option>Select test suite</option>
                    <option>Test suite 1</option>
                    <option>Test suite 2</option>
                    <option>Test suite 3</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="related_ts_desc[][]"></textarea>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="relatedTestSuiteRow">Delete Related Test Suite</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="testSuiteRoleTemplate">
    <div class="form-group testSuiteRoleRow">
        <div class="row">
            <div class="col-md-3">
                <label>Name:</label>
                <input type="text" class="form-control" name="role_names[]" value=""/>
            </div>
            <div class="col-md-5">
                <label>Description:</label>
                <input type="text" class="form-control" name="role_descs[]" value=""/>
            </div>
            <div class="col-md-3">
                <label>Profile types:</label>
                <input type="text" class="form-control" name="role_types[]" value=""/>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testSuiteRoleRow">Delete Test Suite Role</button>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="specificationDocsBoxTemplate">
    <div class="form-group specificationDocRow">
        <div class="row">
            <div class="col-md-4">
                <label>Document Name:</label>
                <input type="text" class="form-control" name="doc_name[]" value=""/>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label>Document Description:</label>
                    <textarea name="doc_desc[]" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Document Location:</label>
                    <input type="text" class="form-control" name="doc_loc[]" value=""/>
                </div>
                <div class="form-group">
                    <div class="upload-file-field">
                        <input type="file" name="doc_file[]" class="input-file" />
                    </div>
                </div>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="specificationDocRow">Delete Document</button>
            </div>
        </div>
    </div>
</script>
@stop