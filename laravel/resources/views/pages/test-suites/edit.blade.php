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
    });
</script>
@stop