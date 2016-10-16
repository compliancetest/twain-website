<form action="#" method="get" id="filterByForm">
    <div class="row">

        <div class="form-group col-sm-12 col-md-12">
            <label for="filterExecutionID">Keyword:</label>
            <input type="text" class="form-control" name="q" id="q"
                   @if($request->get('q')) value="{{ $request->get('q') }}" @endif>
            @if(($request->get('q')) && ($request->get('q') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterType">Type:</label>
            <select class="form-control" id="filterType" name="post_type">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/type/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('post_type') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('post_type')) && ($request->get('post_type') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterOwner">Owner:</label>
            <select class="form-control" id="filterOwner" name="owner">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/owner/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('owner') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('owner')) && ($request->get('owner') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterTestSuite">Test Suite:</label>
            <select class="form-control" id="filterTestSuite" name="test_suite">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/test_suite/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('test_suite') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('test_suite')) && ($request->get('test_suite') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterRole">Role:</label>
            <select class="form-control" id="filterRole" name="role">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/role/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('role') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('role')) && ($request->get('role') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterLevel">Role:</label>
            <select class="form-control" id="filterLevel" name="level">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/level/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('level') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('level')) && ($request->get('level') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterStatus">Status:</label>
            <select class="form-control" id="filterStatus" name="status">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/status/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('status') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('status')) && ($request->get('status') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDate">Last Update:</label>
            <div class="input-group">
                <input type="text" class="form-control" id="filterDate" readonly data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd" name="date_from"
                       placeholder="Date From"
                       @if($request->get('date_from')) value="{{ $request->get('date_from') }}" @endif>
                <span class="input-group-addon filterCalendar"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_from') && $request->get('date_from') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDate">&nbsp;</label>
            <div class="input-group">
                <input type="text" class="form-control col-md-1" id="filterDate" readonly data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd"
                       name="date_to" placeholder="Date To"
                       @if($request->get('date_to')) value="{{ $request->get('date_to') }}" @endif>
                <span class="input-group-addon filterCalendar"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_to') && $request->get('date_to') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
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