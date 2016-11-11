@extends('app')

@section('content')

    <div class="container main-container">
        <div class="main-content edit-test-suite-page block-loading-wrapper">
            <div class="page-title">
                @if($testSuite)
                    <h1>Edit Test Suite: {{ $testSuite->full_name }}</h1>
                @else
                    <h1>Create Test Suite:</h1>
                @endif
            </div>

            {!! Form::model($testSuite, ['data-test-suites' => true, 'data-ajax-form'=>'true', 'data-notification-container'=>'.error-box', 'data-redirect-after-submit' => '/laravel-test-suite/' . $testSuite->slug, 'method' => 'POST', 'url' => '/laravel-test-suite/' . $testSuite->slug]) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="colored-box collapsible-box">
                            <div class="colored-box-header"><a href="#chooseCommunityBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Choose Community</div>
                            <div class="colored-box-body collapse in" id="chooseCommunityBox">
                                <div class="colored-box-content">
                                    <div class="form-group">
                                        <select name="community_id" id="communityId" class="form-control">
                                            @foreach(\App\Community::all()->sortBy('title') as $community)
                                                @if($community->isAdmin() || is_super_admin())
                                                    <option value="{{ $community->id }}" @if($community->id == $testSuite->community_id) selected="selected" @endif>{{ $community->title }}</option>
                                                @endif
                                            @endforeach
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
                                <?php $testSuiteTypes = is_object($testSuite) ? $testSuite->types->keyBy('type') : [];?>
                                <div class="colored-box-content test-suite-types-box">
                                    <div class="checkbox">
                                        <label>
                                            <input name="test_suite_type[]" value="Data Exchange" type="checkbox" @if(isset($testSuiteTypes['Data Exchange'])) checked="checked"@endif>
                                            Data Exchange
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="test_suite_type[]" value="Environment" type="checkbox" @if(isset($testSuiteTypes['Environment'])) checked="checked"@endif>
                                            Environment
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="test_suite_type[]" value="Quality" type="checkbox" @if(isset($testSuiteTypes['Quality'])) checked="checked"@endif>
                                            Quality
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="test_suite_type[]" value="Some Other Type" type="checkbox" @if(isset($testSuiteTypes['Some Other Type'])) checked="checked"@endif>
                                            Some Other Type
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input name="test_suite_type[]" value="Web Technology" type="checkbox" @if(isset($testSuiteTypes['Web Technology'])) checked="checked"@endif>
                                            Web Technology
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Test Suite Information --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testSuiteInformationBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Suite Information</div>
                    <div class="colored-box-body collapse in" id="testSuiteInformationBox">
                        <div class="colored-box-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="testSuiteTitle">Title:</label>
                                        <input type="text" id="testSuiteTitle" name="name" class="form-control" value="{{ $testSuite->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="testSuiteName">Name:</label>
                                        <input type="text" id="testSuiteName" name="short_name" class="form-control" value="{{ $testSuite->short_name }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="testSuitePublishedDate">Published:</label>
                                        <div class="input-group date" data-provide="datepicker">
                                            <input type="text" id="testSuitePublishedDate" name="published_at" class="form-control datepicker-form-control" readonly value="{{ formatDate($testSuite->published_at) }}">
                                            <span class="input-group-addon"><span class="calendar-icon"></span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="testSuiteIssuer">Issuer:</label>
                                        <input type="text" id="testSuiteIssuer" name="issuer" class="form-control" value="{{ $testSuite->issuer }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="testSuiteRevisionDescription">Revision Description:</label>
                                        <input type="text" id="testSuiteRevisionDescription" name="revision_description" class="form-control" value="{{ $testSuite->revision_description }}">
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
                                                    <input type="radio" name="status" value="Draft" @if($testSuite->status == 'Draft') checked="checked"@endif>
                                                    <span class="status status-circle status-draft">D</span>Draft
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" value="Active" @if($testSuite->status == 'Active') checked="checked"@endif>
                                                    <span class="status status-circle status-active">A</span>Active
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" value="Deprecated" @if($testSuite->status == 'Deprecated') checked="checked"@endif>
                                                    <span class="status status-circle status-deprecated">C</span>Deprecated
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" value="Obsolete" @if($testSuite->status == 'Obsolete') checked="checked"@endif>
                                                    <span class="status status-circle status-obsolete">O</span>Obsolete
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="status" value="Partial" @if($testSuite->status == 'Partial') checked="checked"@endif>
                                                    <span class="status status-circle status-partial">P</span>Partial
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-1">
                                    <div class="form-group">
                                        <label for="testSuiteVersion">Version:</label>
                                        <div class="manage-version-box row">
                                            <div class="manage-version-group">
                                                <span>Major</span>
                                                <input type="text" id="tsVersionMajor" name="version_major" class="form-control" readonly="readonly" value="{{ intval($testSuite->version_major) }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tsVersionMajor"
                                                @if($testSuite && $testSuite->isNextVersionExist('version_major')) disabled="disabled" data-tooltip="tooltip" title="Later version already exists." @endif></button>
                                            </div>
                                            <div class="manage-version-group">
                                                <span>Minor</span>
                                                <input type="text" id="tsVersionMinor" name="version_minor" class="form-control" readonly="readonly" value="{{ intval($testSuite->version_minor) }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tsVersionMinor"
                                                @if($testSuite && $testSuite->isNextVersionExist('version_minor')) disabled="disabled" data-tooltip="tooltip" title="Later version already exists." @endif></button>
                                            </div>
                                            <div class="manage-version-group">
                                                <span>Patch</span>
                                                <input type="text" id="tsVersionPatch" name="version_patch" class="form-control" readonly="readonly" value="{{ intval($testSuite->version_patch) }}" />
                                                <button type="button" class="btn btn-primary btn-icon btn-add" data-version-id="tsVersionPatch"
                                                @if($testSuite && $testSuite->isNextVersionExist('version_patch')) disabled="disabled" data-tooltip="tooltip" title="Later version already exists." @endif></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Product Type:</label>
                                <div class="radio-group">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="product_type" value="DataSource" @if($testSuite->product_type == 'DataSource') checked="checked" @endif>
                                            DataSource
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="product_type" value="Application" @if($testSuite->product_type == 'Application') checked="checked" @endif>
                                            Application
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="testSuiteDescription">Description:</label>
                                <textarea rows="5" class="form-control" id="testSuiteDescription" name="description">{!! $testSuite->description !!}</textarea>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Conformance Levels --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#conformanceLevelsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Conformance Levels</div>
                    <div class="colored-box-body collapse in" id="conformanceLevelsBox">
                        <div class="colored-box-content dynamic-rows" id="conformanceLevelContent">


                            @if($testSuite->conformanceLevels)
                                @foreach($testSuite->conformanceLevels as $conformanceLevel)
                                    <div class="form-group conformance-level-row">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label>Conformance Level Code:</label>
                                                <input type="text" class="form-control" name="conformanceLevels[code][]" value="{{ $conformanceLevel->code }}"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Conformance Level Description:</label>
                                                <textarea rows="3" class="form-control" name="conformanceLevels[description][]">{{ $conformanceLevel->description }}</textarea>
                                            </div>
                                            @if($conformanceLevel->code != 'Default')
                                                <div class="col-md-1 action-col">
                                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="conformance-level-row">Delete Conformance Level</button>
                                                </div>
                                            @endif
                                            <input type="hidden" name="conformanceLevels[id][]" value="{{ $conformanceLevel->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        <div class="colored-box-footer">
                            <button type="button" onclick="Page.helpers.addRow('#conformanceLevelTemplate','#conformanceLevelContent');" class="btn btn-success btn-with-icon btn-add">New Conformance Level</button>
                        </div>
                    </div>
                </div>

                {{-- Features List --}}
                <div class="colored-box collapsible-box" id="featuresBox">
                    <div class="colored-box-header"><a href="#featuresListBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Features List</div>
                    <div class="colored-box-body collapse in" id="featuresListBox">
                        <div class="colored-box-content dynamic-rows" id="featuresListContent">

                            @if($testSuite->features)
                                @foreach($testSuite->features as $feature)
                                    <div class="form-group featureRow">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label>Feature Name:</label>
                                                <input type="text" class="form-control" name="features[name][]" value="{{ $feature->name }}"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Description:</label>
                                                <textarea rows="3" class="form-control" name="features[description][]">{!! $feature->description !!}</textarea>
                                            </div>
                                            <div class="col-md-1 action-col">
                                                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="featureRow">Delete Feature</button>
                                            </div>
                                            <input type="hidden" name="features[id][]" value="{{ $feature->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif

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

                            @if($testSuite->scenarios)
                                @foreach($testSuite->scenarios as $scenario)
                                    <div class="form-group scenarioRow">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Code:</label>
                                                <input type="text" class="form-control" name="scenarios[code][]" value="{{ $scenario->code }}"/>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Description:</label>
                                                <textarea rows="3" class="form-control" name="scenarios[description][]">{!! $scenario->description  !!}</textarea>
                                            </div>
                                            <div class="col-md-1">
                                                <label>Sequence:</label>
                                                <input type="text" class="form-control" name="scenarios[sequence][]" value="{{ $scenario->sequence }}"/>
                                            </div>
                                            @if($scenario->code != 'Default')
                                                <div class="col-md-1 action-col">
                                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="scenarioRow">Delete Scenario</button>
                                                </div>
                                            @endif
                                            <input type="hidden" name="scenarios[id][]" value="{{ $scenario->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="colored-box-footer">
                            <button type="button" onclick="Page.helpers.addRow('#scenarioTemplate','#scenariosContent');" class="btn btn-success btn-with-icon btn-add">New Scenario</button>
                        </div>
                    </div>
                </div>

                <div class="block-loading-wrapper" id="profileTypesContent">
                    @include('pages.test-suites.partials.community-profile-types', ['suiteCommunity' => $suiteCommunity])
                </div>

                {{-- Test Cases --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testCasesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Test Cases</div>
                    <div class="colored-box-body collapse in" id="testCasesBox">
                        <div class="colored-box-content">
                            <ul class="test-suites-cases-list clearfix">
                                @if($testSuite->testCases)
                                    @foreach($testSuite->testCases as $testCase)
                                        <li><a href="/laravel-test-case/{{ $testCase->slug }}" target="_blank">{{ $testCase->full_name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                            <a href="/laravel-test-case/create" class="btn btn-success btn-with-icon btn-add">New Test Case</a>
                        </div>
                    </div>
                </div>

                {{-- Related Test Suites --}}
                <div class="block-loading-wrapper" id="testSuitesContent">
                    <div class="colored-box collapsible-box">
                        <div class="colored-box-header"><a href="#relatedTestSuitesBox" class="collapse-arrow" data-toggle="collapse"><span
                                        class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Related Test Suites
                        </div>
                        <div class="colored-box-body collapse in" id="relatedTestSuitesBox">
                            <div class="colored-box-content dynamic-rows" id="relatedTestSuitesContent">

                                @if($testSuite->relatedTestSuites)
                                    @foreach($testSuite->relatedTestSuites as $relatedTestSuite)
                                        <div class="form-group relatedTestSuiteRow">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label>Related Suite:</label>
                                                     <div class="relatedSuitesList">
                                                        @include('pages.test-suites.partials.community-test-suites')
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Description:</label>
                                                    <textarea rows="3" class="form-control" name="related_ts[description][]">{!! $relatedTestSuite->description !!}</textarea>
                                                </div>
                                                <div class="col-md-1 action-col">
                                                    <button class="btn btn-primary btn-icon btn-delete" data-delete-row="relatedTestSuiteRow">Delete Related Test Suite</button>
                                                </div>
                                                <input type="hidden" name="related_ts[id][]" value="{{ $relatedTestSuite->id }}">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="block-loading relatedTestSuites">
                                    <div class="loading-content"><span class="loader"></span>
                                        <div class="loading-text">LOADING DATA</div>
                                        <div class="loading-wait">Please wait...</div>
                                    </div>
                                </div>

                            </div>
                            <div class="colored-box-footer">
                                <button type="button" onclick="Page.helpers.addRow('#relatedTestSuiteTemplate','#relatedTestSuitesContent');"
                                        class="btn btn-success btn-with-icon btn-add">New Test Suite
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Roles --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#testSuitesRolesBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Roles</div>
                    <div class="colored-box-body collapse in" id="testSuitesRolesBox">
                        <div class="colored-box-content dynamic-rows" id="testSuitesRolesContent">

                            @if($testSuite->roles)
                                @foreach($testSuite->roles as $role)
                                    <div class="form-group testSuiteRoleRow">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Name:</label>
                                                <input type="text" class="form-control" name="roles[name][]" value="{{ $role->name }}"/>
                                            </div>
                                            <div class="col-md-7">
                                                <label>Description:</label>
                                                <input type="text" class="form-control" name="roles[description][]" value="{!! $role->description !!}"/>
                                            </div>
                                            <div class="col-md-1 action-col">
                                                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="testSuiteRoleRow">Delete Test Suite Role</button>
                                            </div>
                                            <input type="hidden" name="roles[id][]" value="{{ $role->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif

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

                            @if($testSuite->specificationDocuments)
                                @foreach($testSuite->specificationDocuments as $specificationDocument)
                                    <div class="form-group specificationDocRow">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Document Name:</label>
                                                <input type="text" class="form-control" name="specificationDocuments[name][]" value="{{ $specificationDocument->name }}"/>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label>Document Description:</label>
                                                    <textarea name="specificationDocuments[description][]" class="form-control"
                                                              rows="3">{!! $specificationDocument->description !!}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Document Location:</label>
                                                    <input type="text" class="form-control" name="specificationDocuments[link][]" value="{{ $specificationDocument->link }}"/>
                                                </div>
                                            </div>
                                            <div class="col-md-1 action-col">
                                                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="specificationDocRow">Delete Document</button>
                                            </div>
                                            <input type="hidden" name="specificationDocuments[id][]" value="{{ $specificationDocument->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        <div class="colored-box-footer">
                            <button type="button" onclick="Page.helpers.addRow('#specificationDocsBoxTemplate','#specificationDocsBoxContent'); customizeFileTag();" class="btn btn-success btn-with-icon btn-add">New Document</button>
                        </div>
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#excerptBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Excerpt</div>
                    <div class="colored-box-body collapse in" id="excerptBox">
                        <div class="colored-box-content">

                            <div class="form-group featureRow">
                                <div class="row">
                                    <div class="col-md-7">
                                        <textarea rows="3" class="form-control" name="excerpt">{{ $testSuite->excerpt }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="field-light-description">Excerpts are optional hand-crafted summaries of your content that can be used in your theme.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Protocol Versions --}}
                <div class="colored-box collapsible-box">
                    <div class="colored-box-header"><a href="#protocolVersionsBox" class="collapse-arrow" data-toggle="collapse"><span class="glyphicon glyphicon-triangle-bottom"></span><span class="glyphicon glyphicon-triangle-right"></span></a>Protocol Versions</div>
                    <div class="colored-box-body collapse in" id="protocolVersionsBox">
                        <div class="colored-box-content">

                            <div class="form-group featureRow">
                                <div class="row">
                                    <div class="col-md-7">
                                        <textarea rows="3" class="form-control" name="protocol_versions">
                                            @if($testSuite)
                                                {{ implode(',', $testSuite->protocolVersions()->pluck('version')->toArray()) }}
                                            @endif
                                        </textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="field-light-description">List of supported protocol versions could be just a list of strings(separated by comma) with the following format: ProtocolMajor.ProtocolMinor For example: 2.3,2.3 Empty list of supported protocol versions is allowed and means that a test suite supports any version.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="error-box"></div>

                <div class="form-actions">
                    <div class="checkbox pull-right">
                        @if($testSuite)
                            <label><input type="checkbox" value="1" name="send-notification"> Send Notification to members</label>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save Test Suite</button>
                    <a href="javascript:history.back();" class="btn btn-default btn-with-icon btn-cancel">Cancel</a>
                </div>

                @include('loader', ['loaderClass' => 'form-loading', 'loaderMessage' => 'SAVING...'])

            {{ Form::close() }}
        </div>
    </div>
@stop

@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('body').on('click', '[data-delete-row]', function () {
            var parenElement = '.' + $(this).data('delete-row');
            $(this).parents(parenElement).fadeOut('fast', function () {
                $(this).remove();
            });
            return false;
        });

        $('.manage-version-group .btn-add').click(function (e) {
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
        });

        $('body').on('change', '#communityId', function(e){
            jQuery('.profileTypes.block-loading').show();
            jQuery.ajax({
                url: '/laravel-test-suite/{{ $testSuite ? $testSuite->slug : 'create' }}/community-profiles/' + $('#communityId').val(),
                type: 'get',
                dataType: 'json',
                success: function (rsp) {
                    $('.profileTypes.block-loading').hide();
                    $('#profileTypesContent').html(rsp.html);
                },
                error: function (jqXHR, status) {
                    $('.profileTypes.block-loading').hide();
                    $('.profileTypes.block-loading').before('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    setTimeout(function () {
                        $('.profileTypes.block-loading > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 3000);
                }
            });
            var loadingBox = jQuery('.relatedTestSuites.block-loading');
            loadingBox.show();
            jQuery.ajax({
                url: '/laravel-test-suite/{{ $testSuite ? $testSuite->slug : 'create' }}/community-test-suites/' + $('#communityId').val(),
                type: 'get',
                dataType: 'json',
                success: function (rsp) {
                    loadingBox.hide();
                    $('.relatedSuitesList').html(rsp.html);
                },
                error: function (jqXHR, status) {
                    loadingBox.hide();
                    loadingBox.before('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    setTimeout(function () {
                        $('#testSuitesContent > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 3000);
                }
            });
        });


        $('body').on('change', 'input[name="product_type"]', function(e){
            if($(this).val() == 'Application'){
                $('#featuresBox').show();
            } else {
                $('#featuresBox').hide();
            }
        });
         $('input[name="product_type"]').change();

    });

</script>
<script type="text/html" id="conformanceLevelTemplate">
    <div class="form-group conformance-level-row">
        <div class="row">
            <div class="col-md-5">
                <label>Conformance Level Code:</label>
                <input type="text" class="form-control" name="conformanceLevels[code][]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Conformance Level Description:</label>
                <textarea rows="3" class="form-control" name="conformanceLevels[description][]"></textarea>
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
                <input type="text" class="form-control" name="features[name][]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="features[description][]"></textarea>
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
                <input type="text" class="form-control" name="scenarios[code][]" value=""/>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="scenarios[description][]"></textarea>
            </div>
            <div class="col-md-1">
                <label>Sequence:</label>
                <input type="text" class="form-control" name="scenarios[sequence][]" value=""/>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="scenarioRow">Delete Scenario</button>
            </div>
        </div>
    </div>
</script>


<div id="relatedTestSuiteTemplate" style="display: none;">
    <div class="form-group relatedTestSuiteRow">
        <div class="row">
            <div class="col-md-5">
                <label>Related Suite:</label>
                <div class="relatedSuitesList">
                    @include('pages.test-suites.partials.community-test-suites')
                </div>
            </div>
            <div class="col-md-6">
                <label>Description:</label>
                <textarea rows="3" class="form-control" name="related_ts[description][]"></textarea>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="relatedTestSuiteRow">Delete Related Test Suite</button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="testSuiteRoleTemplate">
    <div class="form-group testSuiteRoleRow">
        <div class="row">
            <div class="col-md-4">
                <label>Name:</label>
                <input type="text" class="form-control" name="roles[name][]" value=""/>
            </div>
            <div class="col-md-7">
                <label>Description:</label>
                <input type="text" class="form-control" name="roles[description][]" value=""/>
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
                <input type="text" class="form-control" name="specificationDocuments[name][]" value=""/>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label>Document Description:</label>
                    <textarea name="specificationDocuments[description]" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Document Location:</label>
                    <input type="text" class="form-control" name="specificationDocuments[link]" value=""/>
                </div>
            </div>
            <div class="col-md-1 action-col">
                <button class="btn btn-primary btn-icon btn-delete" data-delete-row="specificationDocRow">Delete Document</button>
            </div>
        </div>
    </div>
</script>
@stop