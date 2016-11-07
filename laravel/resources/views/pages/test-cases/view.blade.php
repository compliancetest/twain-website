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
                @can('changeTestCase', $testCase)
                    <a href="/laravel-test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                @endcan
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
                        @foreach($testCase->scenarios as $scenario)
                            <li><strong>{{ $scenario->testSuiteScenario->code }}</strong> ({{ $scenario->testSuiteScenario->description }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="colored-box">
                <div class="colored-box-header">Test Execution</div>
                <div class="colored-box-content">
                    <ul class="list-group row">
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                    </ul>
                </div>
            </div>

            <div class="colored-box">
                <div class="colored-box-header">Test Samples</div>
                <div class="colored-box-content">
                    <ul class="image-gallery-list" id="testSamplesGallery">
                        <li>
                            <a href="http://placehold.it/550x750" title="Test image 1" data-gallery="#blueimp-gallery">
                                <img src="http://placehold.it/550x750" title="" alt="" class="img-fluid">
                            </a>
                        </li>
                        <li>
                            <a href="http://placehold.it/550x550" title="Test image 2" data-gallery="#blueimp-gallery">
                                <img src="http://placehold.it/550x550" title="" alt="" class="img-fluid">
                            </a>
                        </li>
                        <li>
                            <a href="http://placehold.it/450x350" title="Test image 3" data-gallery="#blueimp-gallery">
                                <img src="http://placehold.it/450x350" title="" alt="" class="img-fluid">
                            </a>
                        </li>
                        <li>
                            <a href="http://placehold.it/1000x1500" title="Test image 4" data-gallery="#blueimp-gallery">
                                <img src="http://placehold.it/1000x1500" title="" alt="" class="img-fluid">
                            </a>
                        </li>
                        <li>
                            <a href="http://placehold.it/350x450" title="Test image 5" data-gallery="#blueimp-gallery">
                                <img src="http://placehold.it/350x450" title="" alt="" class="img-fluid">
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="colored-box">
                <div class="colored-box-header">Test Configuration</div>
                <div class="colored-box-content">
                    <ul class="list-group row">
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
                        <li class="list-group-item col-xs-6"><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/668" data-toggle="modal" data-remote="true"
                                                                data-ajax-modal data-target="#testExecutionProfileInstance">SIT-01b_v1.0 TEFC v1.0</a></li>
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

    {{-- Test Execution Profile Instance Detail Modal--}}
    <div class="modal fade profile-modal" id="testExecutionProfileInstance" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                    Profile Instance Detail
                </div>
                <div class="modal-body">
                    <div class="block-loading">
                        <div class="loading-content"><span class="loader"></span>
                            <div class="loading-text">LOADING PROFILE</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                </div>
            </div>
        </div>
    </div>
@stop


@section('page-scripts')
    <!-- The Gallery as lightbox dialog, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <ol class="indicator"></ol>
    </div>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.blueimp-gallery.min.js"></script>
    <script>
        var clipboard = new Clipboard('.copyProfileLink');
        clipboard.on('success', function (e) {
            jQuery(e.trigger).parents('.form-group').before('<div class="success-message">The profile url has been copied to clipboard.</div>');
            var messageTimeoutHandler = setTimeout(function () {
                $('.success-message').slideUp();
            }, 2000);
        });

        jQuery(document).ready(function ($) {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
            });

            $('#testExecutionProfileInstance').on('hidden.bs.modal', function () {
                $(this).find('.modal-body').html('<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
            });

        });
    </script>
@stop