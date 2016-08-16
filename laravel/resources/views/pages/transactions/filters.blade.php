<form action="#" method="get" id="filterByForm">
    <div class="row">

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterProduct">Product:</label>
            <select class="form-control" id="filterProduct" name="product_id">
                <option value="">- All -</option>
                @foreach($filters['product_id'] as $product)
                    <option value="{{ $product->ID }}" @if($request->get('product_id') == $product->ID) selected="selected" @endif>{{ $product->post_title }}</option>
                @endforeach
            </select>
            @if(($request->get('product_id')) && ($request->get('product_id') !== '') && ($request->get('product_id') == $product->ID))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterSuite">Test Suite:</label>
            <select class="form-control" id="filterSuite" name="test_suite_id">
                <option value="">- All -</option>
                @foreach($filters['test_suite_id'] as $testSuite)
                    <option value="{{ $testSuite->ID }}" @if($request->get('test_suite_id') == $testSuite->ID) selected="selected" @endif>{{ $testSuite->post_title }}</option>
                @endforeach
            </select>
            @if(($request->get('test_suite_id')) && ($request->get('test_suite_id') !== '') && ($request->get('test_suite_id') == $testSuite->ID))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterCase">Test Case:</label>
            <select class="form-control" id="filterCase" name="test_case_id">
                <option value="">- All -</option>
                @foreach($filters['test_case_id'] as $testCase)
                    <option value="{{ $testCase->ID }}" @if($request->get('test_case_id') == $testCase->ID) selected="selected" @endif>{{ $testCase->post_title }}</option>
                @endforeach
            </select>
            @if(($request->get('test_case_id')) && ($request->get('test_case_id') !== '') && ($request->get('test_case_id') == $testCase->ID))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDate">Date:</label>
            <div class="input-group">
                <input type="text" class="form-control" id="filterDate" readonly data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd" name="date"
                       @if($request->get('date')) value="{{ $request->get('date') }}" @endif>
                <span class="input-group-addon" id="filterCalendar"><span class="calendar-icon"></span></span>
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

        @if($supportOrAdmin)

            <div class="form-group col-sm-6 col-md-3">
                <label for="filterSubscription">Subscription:</label>
                <select class="form-control" id="filterSubscription" name="subscription_id">
                    <option value="">- All -</option>
                    @foreach($filters['subscription_id'] as $subscription)
                        <option value="{{ $subscription->id }}"
                                @if($request->get('subscription_id') == $subscription->id) selected="selected" @endif>{{ $subscription->nickname }}</option>
                    @endforeach
                </select>
                @if(($request->get('subscription_id')) && ($request->get('subscription_id') !== '') && ($request->get('subscription_id') == $subscription->id))<span class="clear-filter" title="Clear Filter">X</span>@endif
            </div>
        @endif

    </div>

    @if($supportOrAdmin)

        <div class="row">
            <div class="form-group col-sm-6 col-md-3">
                <label for="filterOrganisation">Organisation:</label>
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

    <div class="transaction-filter-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
    </div>
</form>

<div class="block-loading" id="filterBySpinner">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING FILTERS</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>