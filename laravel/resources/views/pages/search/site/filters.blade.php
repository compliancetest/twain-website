<form action="/search-results/logs-list" method="get" id="siteSearchFilterByForm" data-search-filter="/search-results/filters" data-search-name="siteSearch">
    <div class="row">

        <div class="form-group col-sm-12 col-md-12">
            <label for="filterExecutionID">Keyword:</label>
            <input type="text" class="form-control" name="q" id="keyword"
                   @if($request->get('q')) value="{{ $request->get('q') }}" @endif>
            @if(($request->get('q')) && ($request->get('q') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterType">Type:</label>
            <select class="form-control" id="filterType" name="post_type">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/post_type/buckets') as $postType)
                    <option value="{{ $postType['value'] }}" @if($request->get('post_type') == $postType['value']) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('post_type')) && ($request->get('post_type') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="filterType">Community:</label>
            <select class="form-control" id="filterCommunity" name="community_id">
                <option value="">- All -</option>
                @foreach($results->getPath('facets/community/buckets') as $postType)
                    <?php $communityId = \App\Community::where('title', $postType['value'])->first()->id;?>
                    <option value="{{ $communityId }}"
                            @if(!empty($request->get('community_id')) && $request->get('community_id') == $communityId) selected="selected" @endif>{{ $postType['value'] }}</option>
                @endforeach
            </select>
            @if(($request->get('community_id')) && ($request->get('community_id') !== ''))<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="siteSearchDateFrom">Last Update:</label>
            <div class="input-group date" data-provide="datepicker">
                <input type="text" class="form-control datepicker-form-control" id="siteSearchDateFrom" readonly name="date_from"
                       placeholder="Date From"
                       @if($request->get('date_from')) value="{{ $request->get('date_from') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_from') && $request->get('date_from') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>

        <div class="form-group col-sm-6 col-md-3">
            <label for="siteSearchDateTo">&nbsp;</label>
            <div class="input-group date" data-provide="datepicker">
                <input type="text" class="form-control col-md-1 datepicker-form-control" id="siteSearchDateTo" readonly
                       name="date_to" placeholder="Date To"
                       @if($request->get('date_to')) value="{{ $request->get('date_to') }}" @endif>
                <span class="input-group-addon"><span class="calendar-icon"></span></span>
            </div>
            @if($request->get('date_to') && $request->get('date_to') !== '')<span class="clear-filter" title="Clear Filter">X</span>@endif
        </div>
    </div>
    <input type="hidden" name="orderby" id="orderby" value="{{ $request->get('orderby') }}">
    <input type="hidden" name="order" id="order" value="{{ $request->get('order') }}">

    <div class="filter-box-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        &nbsp;&nbsp;
        <button type="reset" class="btn btn-default btn-with-icon btn-clear">Clear</button>
    </div>
</form>

<div class="block-loading" id="siteSearchFilterSpinner">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING FILTERS</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>