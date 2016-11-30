@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-case-view">
            <div class="page-title">
                <ul class="pull-right">
                    @foreach($testSuites as $testSuite)
                        <li class="noprint">Go To <a href="/test-suite/{{ $testSuite->slug }}">{{ $testSuite->full_name }}</a></li>
                    @endforeach
                </ul>
                <h1>Test case: <strong>{{ $testCase->full_name }}</strong></h1>
                @can('changeTestCase', $testCase)
                    <a href="/test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit noprint">Edit</a>
                    <a data-target="#deleteTestCaseModal" class="btn btn-danger btn-with-icon btn-delete noprint"
                       data-tooltip="tooltip" href="/test-case/{{ $testCase->slug }}/delete" data-toggle="modal"
                       data-remote="true" data-ajax-modal data-original-title="Delete Test Case">Delete
                    </a>
                    @include('pages.test-cases.partials.delete-test-case-popup')
                @endcan
                &nbsp;
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print noprint">Print</button>
            </div>

            <div class="test-case-description">
                {{ $testCase->description }}
            </div>

            <div class="options-box test-case-options-box">
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
                        @if($testCase->harness_role)
                            <li>Harness Role: <strong>{{ $testCase->harness_role }}</strong></li>
                        @endif
                        @if($testCase->initiator)
                            <li>Initiator: <strong>{{ $testCase->initiator }}</strong></li>
                        @endif
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
                        <li>Execution mode: <strong>{{ $testCase->execution_mode }}</strong></li>
                        <li>Optional: <strong>{{ $testCase->is_optional ? 'Yes' : 'No' }}</strong></li>
                    </ul>
                </div>
                <div class="options-box-row">
                    <div class="options-box-row-title">Scenario:</div>
                    <ul class="inline-options-list scenario-list">
                        <?php $scenarious = $request->get('suite_minor_family_mark') ? $testCase->scenarios->filter(function ($item) use ($request) {
                            return $item->testSuiteScenario->test_suite_id == $request->get('suite_minor_family_mark');
                        }) : $testCase->scenarios;?>
                        @foreach($scenarious as $scenario)
                            <li><strong>{{ $scenario->testSuiteScenario->code }}</strong> ({{ $scenario->testSuiteScenario->description }})</li>
                        @endforeach
                    </ul>
                </div>

                @if(count($testCase->capabilities))
                <div class="options-box-row">
                    <div class="options-box-row-title">Capabilities:</div>
                    <ul class="inline-options-list capabilities-list">
                        @foreach($testCase->capabilities as $cap)
                            <li>{{ $cap->capability }}@if ($cap !== $testCase->capabilities->last()), @endif</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($testCase->test_execution_profile_id)
                    <div class="options-box-row noprint">
                        <div class="options-box-row-title">Test Execution:</div>
                        <ul class="inline-options-list">
                                <li><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/{{ $testCase->test_execution_profile_id }}" data-toggle="modal" data-remote="true"
                                                    data-ajax-modal data-target="#testExecutionProfileInstance">{{ \App\Profile::find($testCase->test_execution_profile_id)->profile_name }}</a></li>
                        </ul>
                    </div>
                @endif

                @if($testCase->configuration_profile_id)
                    <div class="options-box-row noprint">
                        <div class="options-box-row-title">Test Configuration:</div>
                        <ul class="inline-options-list">
                                <li><a href="{{ getSiteUrl() }}/communityprofiles/twain/viewprofile/{{ $testCase->configuration_profile_id }}" data-toggle="modal" data-remote="true"
                                                    data-ajax-modal data-target="#testExecutionProfileInstance">{{ \App\Profile::find($testCase->configuration_profile_id)->profile_name }}</a></li>
                        </ul>
                    </div>
                @endif
            </div>

            @if(count($testCase->samples))
                <div class="colored-box">
                    <div class="colored-box-header">Test Samples</div>
                    <div class="colored-box-content">
                        <ul class="image-gallery-list" id="testSamplesGallery">
                            @foreach($testCase->samples as $sample)
                                <?php $link = $testCase->getSampleLink($sample->image);?>
                                <li>
                                    <a href="{{ $link }}" title="{{ $sample->description }}" data-gallery="#blueimp-gallery">
                                        <img src="{{ $link }}" title="{{ $sample->description }}" alt="" class="img-fluid">
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(count($testCase->features))
                <div class="colored-box">
                    <div class="colored-box-header">Features</div>
                    <div class="colored-box-content">
                        <div class="table-responsive">
                            <table class="table colored-table steps-table">
                                <thead>
                                    <tr>
                                        <th class="col-sm-4 text-left">Test Suite</th>
                                        <th class="text-left">Features</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testSuites as $testSuite)
                                        <?php $selectedFeatures = $testCase->features()->whereIn('test_suites_feature_id', $testSuite->features()->pluck('id')->toArray())->with(['testSuiteFeature'])->get();?>
                                        @if(count($testSuite->features) && count($selectedFeatures))
                                            <tr>
                                                <td>{{ $testSuite->full_name }}</td>
                                                <td>
                                                    @foreach($selectedFeatures as $k => $feature)
                                                        <span data-tooltip="tooltip" title="{{ $feature->testSuiteFeature->description }}">{{ $feature->testSuiteFeature->name }}</span>@if ($k != count($selectedFeatures) - 1), @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

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
                                    <td class="text-center">{{ $testStep->step }}</td>
                                    <td>{{ $testStep->action }}</td>
                                    <td>{{ $testStep->expected_result }}</td>
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