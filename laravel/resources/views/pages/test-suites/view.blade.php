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
                    {{--<li>Test Tool: <a href="#" data-tooltip="tooltip">DTS-DS-Installer-1.0.exe</a></li>--}}
                    {{--<li>Test Tool(X64): <a href="#" data-tooltip="tooltip">DTS-DS-Installer-1.0_X64.exe</a></li>--}}
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
            
            <div class="row test-cases-list-header">
                <div class="col-md-4 item-subtitle">Test Cases</div>
                <div class="col-md-8 text-right">
                    <div class="form-inline">
                        <label>Filter By:</label>
                        <div class="form-group">
                            <select name="scenario" class="form-control">
                                <option value="">- Scenario -</option>
                                @foreach($testSuite->scenarios as $scenario)
                                    <option value="{{ $scenario->id }}">{{ $scenario->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="conformance" class="form-control">
                                <option value="">- Conformance Level -</option>
                                <option value="Default">Default</option><option value="A">A</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="tc_status" class="form-control">
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
                    </div>
                </div>
            </div>

            <div class="blue-colored-table-wrapper table-responsive">
                <table class="table blue-colored-table test-cases-table">
                    <thead>
                        <tr>
                            <th class="text-left">Test Scenario</th>
                            <th class="text-left">Test Case</th>
                            <th>Tester Role</th>
                            <th>Conf Levels</th>
                            <th>Outcome Type</th>
                            <th>Test Pattern</th>
                            <th class="text-left">Test Intent Description</th>
                            @can('change', $testSuite)
                                <th>Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        <?php $scenarioId = false;?>
                        <?php $testCases = $testSuite->getCases($filters, $isAdmin)->paginate(25);?>
                        @foreach($testCases  as $index => $testCase)
                        <tr>
                            @if($scenarioId != $testCase->scenarioId)
                                <?php $scenarioId = $testCase->scenarioId;?>
                                    <td rowspan="{{ $testCases->filter(function ($value) use ($scenarioId) {
                                            return $value->scenarioId == $scenarioId;
                                        })->count() }}" class="rowspan-cell">
                                    <?php $scenario = \App\TestSuiteScenarios::find($testCase->scenario->test_suites_scenario_id);?>
                                    <strong>{{ $scenario->code }}:</strong><br /> {!! $scenario->description !!}
                                </td>
                            @endif
                            <td><span class="status status-circle status-{{ strtolower($testCase->status) }}" data-tooltip="tooltip" title="{{ $testCase->status }}">{{ substr($testCase->status, 0, 1) }}</span><a href="/laravel-test-case/{{ $testCase->slug }}">{{ $testCase->full_name }}</a></td>
                            <td class="text-center">{{ $testCase->tester_role}}</td>
                            <td class="text-center">
                                {{ implode(', ', array_unique($testCase->getConformanceLevels($isAdmin)->pluck('code')->toArray())) }}
                            </td>
                            <td class="text-center">{{ $testCase->outcome_type }}</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Tester Initiated 1-Way Notification pattern represents the case where a tester sends a single message (eg a initiate.rollover.request) to the the harness, which performs a set of validations and then stores the result for the tester to view via the message log. There is no correlated response message.">
                                    <span class="test-pattern-icon test-pattern-1"></span>
                                </a>
                            </td>
                            <td>{!! $testCase->description !!}</td>
                            @can('change', $testSuite)
                                <td class="text-center">
                                    <a href="/laravel-test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                    <button type="button" data-toggle="modal" data-target="#deleteTestCaseModal{{$index}}" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</button>
                                    {{-- Delete Test Case Modal--}}
                                    <div class="modal fade" id="deleteTestCaseModal{{$index}}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content block-loading-wrapper">
                                                <div class="modal-header">
                                                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                                    Delete Test Case
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want delete {{ $testCase->full_name }}?
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="#DELETE_URL" class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                                                    <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $testCases->links() }}
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
    });
</script>
@stop