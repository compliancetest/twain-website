@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-suite-view">
            <div class="page-title">
                <a href="/communities/{{ \App\Community::find($testSuite->community_id)->slug }}" class="btn btn-primary btn-print pull-right">Community Home Page</a>

                <h1>{{ $testSuite->full_name }}</h1>
                @can('change', $testSuite)
                    <a href="/laravel-test-suite/{{ $testSuite->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                @endcan
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print">Print</button>
            </div>

            <div class="options-box">
                <ul class="inline-options-list">
                    <li>ID: <strong>{{ $testSuite->short_name }}</strong></li>
                    <li>Published: <strong>{{$testSuite->published_at}}</strong></li>
                    <li>Issuer: <a href="/laravel-test-suites/?issuer={{ $testSuite->issuer }}">{{ $testSuite->issuer }}</a></li>
                    <li>Status: <span class="status status-active">{{ $testSuite->status }}</span></li>
                    <li>Revision: <strong>{{ $testSuite->revision_description }}</strong></li>
                    @if(!$testSuite->protocolVersions->isEmpty())
                        <li>Protocol Versions: <strong>{{ $testSuite->protocolVersions }}</strong></li>
                    @endif
                    @include('pages.test-suites.partials.download-test-tool', ['installer' => $installer, 'x64' => false])
                    @include('pages.test-suites.partials.download-test-tool', ['installer' => $installerX64, 'x64' => true])
                </ul>
            </div>

            <div class="test-suite-description">{!! $testSuite->description !!}</div>
            <div class="simple-tabs">
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

            @if(Auth::check())
                @if(Auth::user()->getSuiteSubscription($testSuite))
                    <!-- todo-migration release link with confirmation popup -->
                    @include('pages.test-suites.partials.release-subscription')
                @else
                    @if(Auth::user()->doesUserOrganisationApproved($testSuite))
                        <!-- todo-migration assign subscription link with conformation popup -->
                        @include('pages.test-suites.partials.assign-subscription')
                    @else
                        @include('pages.test-suites.partials.contact-us-button')
                    @endif
                @endif
            @else
                @include('pages.test-suites.partials.contact-us-button')
            @endif

            <div class="row test-cases-list-header">
                <div class="col-md-4 item-subtitle">Test Cases</div>
                <div class="col-md-8 text-right">
                    <form class="form-inline" id="suiteTestCasesForm">
                        <label>Filter By:</label>
                        <div class="form-group">
                            <select name="scenario" class="form-control">
                                <option value="">- Scenario -</option>
                                @foreach($testSuite->scenarios as $scenario)
                                    <?php if(!$isAdmin && $scenario->code == 'Default') continue;?>
                                    <option value="{{ $scenario->code }}">{{ $scenario->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="conformance_level" class="form-control">
                                <option value="">- Conformance Level -</option>
                                 @foreach($testSuite->conformanceLevels as $conformanceLevel)
                                    <?php if(!$isAdmin && $conformanceLevel->code == 'Default') continue;?>
                                    <option value="{{ $conformanceLevel->code }}">{{ $conformanceLevel->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">- Status -</option>
                                <option value="Active">Active</option>
                                <option value="Draft">Draft</option>
                                <option value="Build">Build</option>
                                <option value="Deprecated">Deprecated</option>
                                <option value="Obsolete">Obsolete</option>
                            </select>
                        </div>
                         @can('change', $testSuite)
                            <a href="#" class="btn btn-success btn-with-icon btn-add">New Test Case</a>
                         @endcan
                    </form>
                </div>
            </div>

            <div id="suiteCases">
                @include('pages.test-suites.partials.test-cases-list')
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
            <!--todo-migration fix loading div - it should appear in test cases section-->
            $('#loadTestCasesResultsSpinner').show();
            $.ajax({
                url: '/laravel-test-suite/{{ $testSuite->slug }}/get-test-cases',
                type: 'get',
                data: data, //$('#suiteTestCasesForm').serialize(),
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

    });
</script>
@stop