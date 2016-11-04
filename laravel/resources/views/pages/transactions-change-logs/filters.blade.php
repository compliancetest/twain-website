<form action="#" method="get" id="filterByForm">
    <div class="row">

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterExecutionID">Execution Id:</label>
            <input type="text" class="form-control" name="execution_id" id="filterExecutionID" style="width: "
                   @if($request->get('execution_id')) value="{{ $request->get('execution_id') }}" @endif>
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
            <div class="input-group date" data-provide="datepicker">
                <input type="text" class="form-control datepicker-form-control" id="filterDate" readonly name="date"
                       @if($request->get('date')) value="{{ $request->get('date') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if(($request->get('date')) && ($request->get('date') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
    </div>


    <div class="filter-box-footer">
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