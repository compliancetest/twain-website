@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-suite-view">
            <div class="page-title">
                <a href="/communities/{{ \App\Community::find($testSuite->community_id)->slug }}" class="btn btn-primary btn-print pull-right noprint">Community Home Page</a>

                <h1>{{ $testSuite->full_name }}</h1>
                @can('changeTestSuite', $testSuite)
                    <a href="/test-suite/{{ $testSuite->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit noprint">Edit</a>
                @endcan
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print noprint">Print</button>
            </div>

            <div class="options-box">
                <ul class="inline-options-list">
                    <li>ID: <strong>{{ $testSuite->short_name }}</strong></li>
                    <li>Published: <strong>{{$testSuite->published_at}}</strong></li>
                    <li class="noprint">Issuer: <a href="/test-suites/?issuer={{ $testSuite->issuer }}">{{ $testSuite->issuer }}</a></li>
                    <li>Status: <span class="status status-{{ strtolower($testSuite->status) }}">{{ $testSuite->status }}</span></li>
                    <li>Revision: <strong>{{ $testSuite->revision_description }}</strong></li>
                    @if(!$testSuite->protocolVersions->isEmpty())
                        <li>Protocol Versions: <strong>{{ implode(', ', $testSuite->protocolVersions()->pluck('version')->toArray() ) }}</strong></li>
                    @endif
                    @include('pages.test-suites.partials.download-test-tool', ['installer' => $installer, 'x64' => false])
                    @include('pages.test-suites.partials.download-test-tool', ['installer' => $installerX64, 'x64' => true])
                </ul>
            </div>

            <div class="test-suite-description">{!! $testSuite->description !!}</div>
            <div class="simple-tabs noprint">
                <ul class="simple-tabs-nav ts-simple-tabs" role="tablist">
                    @if(count($testSuite->roles) > 0)
                        <li><a href="#ts-test-suite-roles">Test Suite Roles</a></li>
                    @endif
                    @if(count($testSuite->conformanceLevels) > 0)
                        <li><a href="#ts-conformance-levels">Conformance Levels</a></li>
                    @endif
                    @if(count($testSuite->getProfileTypes()) > 0)
                        <li><a href="#ts-profile-types">Profile Types</a></li>
                    @endif
                    @if(count($testSuite->specificationDocuments) > 0)
                        <li><a href="#ts-specification-documents">Specification Documents</a></li>
                    @endif
                </ul>
                <div class="tab-content simple-tab-content">
                    @if(count($testSuite->roles) > 0)
                    <div role="tabpanel" class="tab-pane" id="ts-test-suite-roles">
                        @foreach($testSuite->roles as $role)
                            <dl class="definition-list">
                                <dt>{{ $role->name }}</dt>
                                <dd>{{ $role->description }}</dd>
                            </dl>
                        @endforeach
                    </div>
                    @endif
                    @if(count($testSuite->conformanceLevels) > 0)
                    <div role="tabpanel" class="tab-pane" id="ts-conformance-levels">
                        @foreach($testSuite->conformanceLevels as $conformanceLevel)
                            <dl class="definition-list">
                                <dt>{{ $conformanceLevel->code }}</dt>
                                <dd>{{ $conformanceLevel->description }}</dd>
                            </dl>
                        @endforeach
                    </div>
                    @endif
                    @if(count($testSuite->getProfileTypes()) > 0)
                    <div role="tabpanel" class="tab-pane" id="ts-profile-types">
                       <ul>
                           @foreach($testSuite->getProfileTypes() as $profileType)
                               <li><a href="#">{{ $profileType }}</a></li>
                           @endforeach
                       </ul>
                    </div>
                    @endif
                    @if(count($testSuite->specificationDocuments) > 0)
                    <div role="tabpanel" class="tab-pane" id="ts-specification-documents">
                        <ul class="spec-doc-list">
                            @foreach($testSuite->specificationDocuments as $specificationDocument)
                                <li>
                                    <a href="{{ $specificationDocument->link }}">{{ $specificationDocument->name }}</a>
                                    <p>{!! $specificationDocument->description !!}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            <div class="test-suite-subscription noprint">
                @include('pages.test-suites.partials.subscriptions-section')
            </div>

            <div class="row test-cases-list-header">
                <div class="col-md-4 item-subtitle">Test Cases</div>
                <div class="col-md-8 text-right">
                    <form class="form-inline noprint" id="suiteTestCasesForm">
                        <label>Filter By:</label>
                        <div class="form-group">
                            <select name="scenario" class="form-control">
                                <option value="">-Scenario-</option>
                                @if($isAdmin)
                                    <option value="Default">Default</option>
                                @endif
                                @foreach($testSuite->scenarios->sortBy('code') as $scenario)
                                    <?php if ($scenario->code == 'Default') continue;?>
                                    <option value="{{ $scenario->code }}">{{ $scenario->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="conformance_level" class="form-control">
                                <option value="">-Conformance Level-</option>
                                @if($isAdmin)
                                    <option value="Default">Default</option>
                                @endif
                                @foreach($testSuite->conformanceLevels->sortBy('code') as $conformanceLevel)
                                    <?php if ($conformanceLevel->code == 'Default') continue;?>
                                    <option value="{{ $conformanceLevel->code }}">{{ $conformanceLevel->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">-Status-</option>
                                @foreach($testSuite->getCases(['groupBy' => 'test_cases.status'], $isAdmin)->get()->sortBy('status')->pluck('status') as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                         @can('changeTestSuite', $testSuite)
                            <a href="/test-case/create" class="btn btn-success btn-with-icon btn-add">New Test Case</a>
                         @endcan
                    </form>
                </div>
            </div>

            <div class="block-loading-wrapper">
                <div id="suiteCases">
                    @include('pages.test-suites.partials.test-cases-list')
                </div>
                <div id="loadTestCasesResultsSpinner" class="block-loading">
                    <div class="loading-content"><span class="loader"></span>
                        <div class="loading-text">LOADING DATA</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('page-scripts')
<script>
    jQuery(document).ready(function ($) {
        $('.simple-tabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        });
        $('.simple-tabs-nav li:first-child a').click();

        function loadTestCases(data) {
            $('#loadTestCasesResultsSpinner').show();
            $.ajax({
                url: '/test-suite/{{ $testSuite->slug }}/get-test-cases',
                type: 'get',
                data: data,
                error: function (jqXHR, status) {
                },
                success: function (rsp) {
                    $('#suiteCases').html(rsp.html);
                },
                complete: function () {
                    $('#loadTestCasesResultsSpinner').hide();
                }
            })
        }

        $('#suiteTestCasesForm').on('change', function(){
            loadTestCases($('#suiteTestCasesForm').serialize());
        });

        $('body').on('click', '.pagination a', function(e){
            e.preventDefault();
            loadTestCases($('#suiteTestCasesForm').serialize() + "&page=" + getUrlVar($(this).attr('href'), 'page'));
        });

        $('.downloadAgreeLicenseCheck').click(function() {
            var downloadButton = $(this).data('installer-id');
            if (this.checked) {
                $('.licenseDownloadBtn' + downloadButton).prop("disabled", false);
            } else {
                $('.licenseDownloadBtn' + downloadButton).prop("disabled", true);
            }
        });


        $('body').on('submit', '#confirmSubscriptionForm', function (e) {
            e.preventDefault();
            $(this).find('.error-message').remove();
            if(!$(this).find('#agreeCustomerTerms').prop('checked')) {
                $(this).find('.modal-body').append('<div class="error-message">You must agree to our Terms &amp; Conditions.</div>');
            } else {
                jQuery('#confirmSubscriptionFormSpinner').show();
                $.ajax({
                    url: '/test-suite/{{ $testSuite->slug }}/subscription',
                    type: 'post',
                    data: {
                        'status' : 1
                    },
                    success: function (rsp) {
                        var confirmAccessModal = $('#accessTestHarnessModal');
                        $('#confirmSubscriptionFormSpinner').hide();
                        confirmAccessModal.modal('hide');
                        confirmAccessModal.on('hidden.bs.modal', function (e) {
                            $('.test-suite-subscription').html(rsp.html);
                        })
                    },
                    complete: function () {
                        $('#confirmSubscriptionFormSpinner').hide();
                    }
                })
            }
        });


        $('body').on('submit', '#confirmReleaseSubscriptionForm', function (e) {
            e.preventDefault();
            jQuery('#confirmReleaseSubscriptionSpinner').show();
            $.ajax({
                    url: '/test-suite/{{ $testSuite->slug }}/subscription',
                    type: 'post',
                    data: {},
                    success: function (rsp) {
                        var confirmModal = $('#confirmReleaseSubscriptionModal');
                        $('#confirmReleaseSubscriptionSpinner').hide();
                        confirmModal.modal('hide');
                        confirmModal.on('hidden.bs.modal', function (e) {
                            $('.test-suite-subscription').html(rsp.html);
                        })
                    },
                    complete: function () {
                        $('#confirmReleaseSubscriptionSpinner').hide();
                    }
                })
        });
    });
</script>
@stop