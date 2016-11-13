<form action="/test-suites/logs-list" method="get" id="productsSearchFilterForm" data-search-name="productsSearch" data-search-filter="/test-suites/filters">
    <div class="row">

        <div class="form-group col-sm-12 col-md-6">
            <label for="filterKeyword">Keyword:</label>
            <input class="form-control" name="q" id="filterKeyword" type="text" value="{{ $request->get('q') }}">
            @if(($request->get('q')) && ($request->get('q') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterType">Type:</label>
            <select class="form-control" id="filterType" name="type">
                <option value="">- All -</option>
                @foreach($filters['type'] as $entry)
                    <option value="{{ $entry }}" @if($request->get('type') == $entry) selected="selected" @endif>{{ $entry }}</option>
                @endforeach
            </select>
            @if(($request->get('type')) && ($request->get('type') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterIssuer">Issuer:</label>
            <select class="form-control" id="filterIssuer" name="issuer">
                <option value="">- All -</option>
                @foreach($filters['issuer'] as $entry)
                    <option value="{{ $entry }}" @if($request->get('issuer') == $entry) selected="selected" @endif>{{ $entry }}</option>
                @endforeach
            </select>
            @if(($request->get('issuer')) && ($request->get('issuer') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterStatus">Status:</label>
            <select class="form-control" id="filterStatus" name="status">
                <option value="">- All -</option>
                @foreach($filters['status'] as $entry)
                    <option value="{{ $entry }}" @if($request->get('status') == $entry) selected="selected" @endif>{{ $entry }}</option>
                @endforeach
            </select>
            @if(($request->get('status')) && ($request->get('status') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterCommunity">Community:</label>
            <select class="form-control" id="filterCommunity" name="community_id">
                <option value="">- All -</option>
                @foreach($filters['community_id'] as $entry)
                    <option value="{{ $entry }}" @if($request->get('community_id') == $entry) selected="selected" @endif>{{ \App\Community::find($entry)->title }}</option>
                @endforeach
            </select>
            @if(($request->get('community_id')) && ($request->get('community_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDateFrom">Published Date:</label>
            <div class="input-group date" data-provide="datepicker">
                <input class="form-control datepicker-form-control" id="filterDateFrom" readonly="" name="date_from" placeholder="Date From" type="text"
                       @if($request->get('date_from')) value="{{ $request->get('date_from') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_from') && $request->get('date_from') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
        <div class="form-group col-sm-6 col-md-3">
            <label for="filterDateTo">&nbsp;</label>
            <div class="input-group date" data-provide="datepicker">
                <input class="form-control col-md-1 datepicker-form-control" id="filterDateTo" readonly="" name="date_to" placeholder="Date To" type="text"
                       @if($request->get('date_to')) value="{{ $request->get('date_to') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_to') && $request->get('date_to') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
        <input type="hidden" name="orderby" id="orderby" value="{{ $request->get('orderby') }}">
        <input type="hidden" name="order" id="order" value="{{ $request->get('order') }}">
    </div>

    <div class="filter-box-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
    </div>
</form>

<div class="block-loading" id="productsSearchFilterSpinner">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING FILTERS</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>