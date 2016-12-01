<form action="/transactions/transactions-list" method="get" id="transactionSearchFilterByForm" data-search-filter="/transactions/filters" data-search-name="transactionSearch">
    <div class="row">

        <input type="hidden" name="itemsCount" value="{{ $perPage }}" id="perPage">

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterProduct">Product Name:</label>
            <select class="form-control" id="filterProduct" name="product_id">
                <option value="">- All -</option>
                @foreach($filters['product_id'] as $product)
                    <option value="{{ $product->id }}" @if($request->get('product_id') == $product->id) selected="selected" @endif>{{ $product->full_name }}</option>
                @endforeach
            </select>
            @if(($request->get('product_id')) && ($request->get('product_id') !== '') && ($request->get('product_id') == $product->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterSuite">Test Suite:</label>
            <select class="form-control" id="filterSuite" name="suite_minor_family_mark">
                <option value="">- All -</option>
                @foreach($filters['suite_minor_family_mark'] as $testSuite)
                    <option value="{{ $testSuite->minor_family_mark }}" @if($request->get('suite_minor_family_mark') == $testSuite->minor_family_mark) selected="selected" @endif>{{ \App\LaravelTestSuite::getLatestSuiteForMinorFamilyMark($testSuite->minor_family_mark)->full_name }}</option>
                @endforeach
            </select>
            @if(($request->get('suite_minor_family_mark')) && ($request->get('suite_minor_family_mark') !== '') && ($request->get('suite_minor_family_mark') == $testSuite->minor_family_mark))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterCase">Test Case:</label>
            <select class="form-control" id="filterCase" name="test_case_id">
                <option value="">- All -</option>
                @foreach($filters['test_case_id'] as $testCase)
                    <option value="{{ $testCase->id }}" @if($request->get('test_case_id') == $testCase->id) selected="selected" @endif>{{ $testCase->full_name }}</option>
                @endforeach
            </select>
            @if(($request->get('test_case_id')) && ($request->get('test_case_id') !== '') && ($request->get('test_case_id') == $testCase->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDate">Date:</label>
            <div class="input-group date" data-provide="datepicker">
                <input type="text" class="form-control datepicker-form-control" id="filterDate" readonly name="date"
                       @if($request->get('date')) value="{{ $request->get('date') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if(($request->get('date')) && ($request->get('date') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6 col-md-3">
            <label for="filterAudit">Audit:</label>
            <select class="form-control" id="filterAudit" name="audit_record">
                <option value="">- All -</option>
                @foreach($filters['audit_record'] as $auditRecord)
                    <?php $auditRecord = $auditRecord ? 'yes' : 'no';?>
                    <option value="{{ $auditRecord}}" @if($request->get('audit_record') == $auditRecord) selected="selected" @endif>{{ ucfirst($auditRecord) }}</option>
                @endforeach
            </select>
            @if(($request->get('audit_record')) && ($request->get('audit_record') !== '') && ($request->get('audit_record') == $auditRecord))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterTestOutcome">Test Outcome:</label>
            <select class="form-control" id="filterTestOutcome" name="test_outcome_status_id">
                <option value="">- All -</option>
                @foreach($filters['test_outcome_status_id'] as $testOutcome)
                    <option value="{{ $testOutcome->id }}"
                        @if($request->get('test_outcome_status_id') == $testOutcome->id) selected="selected" @endif>{{ $testOutcome->name }}</option>
                @endforeach
            </select>
            @if(($request->get('test_outcome_status_id')) && ($request->get('test_outcome_status_id') !== '') && ($request->get('test_outcome_status_id') == $testOutcome->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterScenario">Scenario:</label>
            <select class="form-control" id="filterScenario" name="scenario_id">
                <option value="">- All -</option>
                @foreach($filters['scenario_id'] as $scenario)
                    <option value="{{ $scenario->id }}" @if($request->get('scenario_id') == $scenario->id) selected="selected" @endif>{{ $scenario->code }}</option>
                @endforeach
            </select>
            @if(($request->get('scenario_id')) && ($request->get('scenario_id') !== '') && ($request->get('scenario_id') == $scenario->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterExecutionId">Execution ID:</label>
            <input class="form-control" id="filterExecutionId" name="execution_id" value="{{ $request->get('execution_id') }}">
            @if(($request->get('execution_id')) && ($request->get('execution_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

    </div>

    @if($supportOrAdmin)

        <div class="row">
            <div class="form-group col-sm-6 col-md-3">
                <label for="filterSubscription">Subscription Nickname:</label>
                <select class="form-control" id="filterSubscription" name="subscription_id">
                    <option value="">- All -</option>
                    @foreach($filters['subscription_id'] as $subscription)
                        <option value="{{ $subscription->id }}"
                                @if($request->get('subscription_id') == $subscription->id) selected="selected" @endif>{{ $subscription->nickname }}</option>
                    @endforeach
                </select>
                @if(($request->get('subscription_id')) && ($request->get('subscription_id') !== '') && ($request->get('subscription_id') == $subscription->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
            </div>

            <div class="form-group col-sm-6 col-md-3">
                <label for="filterOrganisation">Organization:</label>
                <select class="form-control" id="filterOrganisation" name="organisation_id">
                    <option value="">- All -</option>
                    @foreach($filters['organisation_id'] as $organisation)
                        <option value="{{ $organisation->id }}"
                        @if($request->get('organisation_id') == $organisation->id) selected="selected" @endif>{{ $organisation->organisation_name }}</option>
                    @endforeach
                </select>
                @if(($request->get('organisation_id')) && ($request->get('organisation_id') !== '') && ($request->get('organisation_id') == $organisation->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
            </div>
        </div>
    @endif

    <div class="filter-box-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
    </div>
</form>

<div class="block-loading" id="transactionSearchFilterSpinner">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING FILTERS</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>