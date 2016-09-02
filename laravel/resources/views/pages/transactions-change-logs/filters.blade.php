<form action="#" method="get" id="filterByForm">
    <div class="row">

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterExecutionID">Execution Id:</label>
            <div class="input-group">
                <input type="text" class="form-control" name="execution_id" id="filterExecutionID"
                       @if($request->get('execution_id')) value="{{ $request->get('execution_id') }}" @endif>
            </div>
            @if(($request->get('execution_id')) && ($request->get('execution_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterProduct">User:</label>
            <select class="form-control" id="filterUser" name="user_id">
                <option value="">- All -</option>
                @foreach($filters['user_id'] as $user)
                    <option value="{{ $user }}" @if($request->get('user_id') == $user) selected="selected" @endif>{{ cp_get_user_fullname($user)}}</option>
                @endforeach
            </select>
            @if(($request->get('user_id')) && ($request->get('user_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterSuite">Test Outcome:</label>
            <select class="form-control" id="filterRequestType" name="test_outcome_status_id">
                <option value="">- All -</option>
                @foreach($filters['test_outcome_status_id'] as $testOutcome)
                    <option value="{{ $testOutcome }}" @if($request->get('test_outcome_status_id') == $testOutcome) selected="selected" @endif>{{ \App\TestOutcomeStatus::find($testOutcome)->name }}</option>
                @endforeach
            </select>
            @if(($request->get('test_outcome_status_id')) && ($request->get('test_outcome_status_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
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