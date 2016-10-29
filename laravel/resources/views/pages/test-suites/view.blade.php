@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content test-suite-view">
            <div class="page-title">
                <a href="/communities/{{ \App\Community::find($testSuite->community_id)->slug }}" class="btn btn-primary btn-print pull-right">Community Home Page</a>

                <h1>{{ $testSuite->full_name }}</h1>
                <a href="/laravel-test-suite/{{ $testSuite->slug }}/edit" class="btn btn-primary btn-with-icon btn-edit">Edit</a>
                <button onclick="window.print();" class="btn btn-primary btn-with-icon btn-print">Print</button>
            </div>

            <div class="options-box">
                <ul class="inline-options-list">
                    <li>ID: <strong>{{ $testSuite->short_name }}</strong></li>
                    <li>Published: <strong>2016-01-29</strong></li>
                    <li>Issuer: <a href="/laravel-test-suites/?issuer={{ $testSuite->issuer }}">{{ $testSuite->issuer }}</a></li>
                    <li>Status: <span class="status status-active">{{ $testSuite->status }}</span></li>
                    <li>Revision: <strong>{{ $testSuite->revision_description }}</strong></li>
                    @if(!$testSuite->protocolVersions->isEmpty())
                        <li>Protocol Versions: <strong>{{ $testSuite->protocolVersions }}</strong></li>
                    @endif
                    <li>Test Tool: <a href="#" data-tooltip="tooltip">DTS-DS-Installer-1.0.exe</a></li>
                    <li>Test Tool(X64): <a href="#" data-tooltip="tooltip">DTS-DS-Installer-1.0_X64.exe</a></li>
                </ul>
            </div>

            <div class="test-suite-description">{!! $testSuite->description !!}</div>
            <div class="simple-tabs">
                <ul class="simple-tabs-nav ts-simple-tabs" role="tablist">
                    <li class="active"><a href="#ts-test-suite-roles">Test Suite Roles</a></li>
                    <li><a href="#ts-conformance-levels">Conformance Levels</a></li>
                    <li><a href="#ts-profile-types">Profile Types</a></li>
                    <li><a href="#ts-specification-documents">Specification Documents</a></li>
                </ul>
                <div class="tab-content simple-tab-content">
                    <div role="tabpanel" class="tab-pane active" id="ts-test-suite-roles">
                        @foreach($testSuite->roles as $role)
                            <dl class="definition-list">
                                <dt>{{ $role->name }}</dt>
                                <dd>{{ $role->description }}</dd>
                            </dl>
                        @endforeach
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ts-conformance-levels">
                        @foreach($testSuite->conformanceLevels as $conformanceLevel)
                            <dl class="definition-list">
                                <dt>{{ $conformanceLevel->code }}</dt>
                                <dd>{{ $conformanceLevel->description }}</dd>
                            </dl>
                        @endforeach
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ts-profile-types">
                       <ul>
                           @foreach($testSuite->profileTypes as $profileType)
                               <li><a href="#">{{ \App\ProfileType::find($profileType->profile_type_id)->getTitle() }}</a></li>
                           @endforeach
                       </ul>
                    </div>
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
                                <option value="7">Default</option>
                                <option value="8">SC</option>
                                <option value="9">VC</option>
                                <option value="10">SR</option>
                                <option value="11">ST</option>
                                <option value="12">IT</option>
                                <option value="13">UI</option>
                                <option value="14">CX</option>
                                <option value="15">VN</option>
                                <option value="16">VV</option>
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
                        <a href="#" class="btn btn-success btn-with-icon btn-add">New Test Case</a>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="7" class="rowspan-cell">
                                <strong>SC:</strong><br /> Standard Capability. TWAIN Standard capabilities (ID's with a value less than 0x8000). Ignore Vendor Custom capabilities (ID’s with a value of 0x8000 or greater).
                            </td>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Tester Initiated 1-Way Notification pattern represents the case where a tester sends a single message (eg a initiate.rollover.request) to the the harness, which performs a set of validations and then stores the result for the tester to view via the message log. There is no correlated response message.">
                                    <span class="test-pattern-icon test-pattern-1"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="In the Harness Initiated 1-Way Notification pattern, the harness initiates a single outbound message to the tester system (testers can pull messages or receive a push) for processing by the tester. Testers confirm that their own validation results match those performed on the same message by the harness. There is no response message.">
                                    <span class="test-pattern-icon test-pattern-2"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Tester Initiated 2-Way Asynchronous Transaction represents the case where a tester sends a 1-way message (eg a initiate.rollover.request) to the the harness, which performs a set of validations and then sends an asynchronous correlated 1-way message response (eg initiate.rollover.errorResponse) back to the tester. Testers should confirm that the response message matches the expected response defined by the test case.">
                                    <span class="test-pattern-icon test-pattern-3"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="In the Harness Initiated 2-Way Asynchronous Transaction pattern, the harness sends a single message to the tester. The tester processes the message and sends back a single asynchronous correlated response. The test harness itself also generates an 'expected response' which is compared with the actual response from the tester in order to determine test outcome (match = successful test).">
                                    <span class="test-pattern-icon test-pattern-4"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Tester Initiated Synchronous Query/Response matches the client/server web service pattern where the tester is the client and the response is received in the same http session. The pattern is used for scenarios where a real-time response is feasible (eg ATO SuperTICK). Testers should confirm that the response matches the expected response defined in the test case.">
                                    <span class="test-pattern-icon test-pattern-5"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Harness Initiated Synchronous Query/Response matches the client/server web service pattern where the tester is the server and the response is returned to the harness in the same http session. This pattern is rarely encountered because it would imply that there are many providers of the same synchronous service. The harness generates and expected response which is compared with the actual tester response to determine test outcome.">
                                    <span class="test-pattern-icon test-pattern-6"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="status status-circle status-active" data-tooltip="tooltip" title="Active">A</span><a href="#">SC-01 v1.0</a></td>
                            <td class="text-center">DataSource</td>
                            <td class="text-center">A, Default</td>
                            <td class="text-center">Positive</td>
                            <td class="text-center">
                                <a href="/help-faq/test-patterns/" data-tooltip="tooltip" title="The Long Running Correlated Choreography is a composite pattern that is built from a combination 1-way notifications, 2-way transactions, and synchronous services. This pattern is used only when there is a need to simulate long running stateful conversations (for example employer->default fund->choice fund forwarding of choice superannuation contributions).">
                                    <span class="test-pattern-icon test-pattern-7"></span>
                                </a>
                            </td>
                            <td>Confirm Basic Negotiation with CAP_SUPPORTEDCAPS.</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a href="#" class="btn btn-success btn-icon btn-delete" data-tooltip="tooltip" title="Delete Case">Delete</a>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                <ul class="pagination">
                    <li class="disabled"><span>«</span></li>
                    <li class="active"><span>1</span></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#" rel="next">»</a></li>
                </ul>
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
    });
</script>
@stop
