<div class="table-responsive">
    <div class="blue-colored-table-wrapper">
        <div class="filter-list-actions">
            <div class="col-md-9">
                @if($results->getPath('hits/found'))
                    <div class="filter-results-count">
                        <?php $page = $request->get('page') ? $request->get('page') : 1;?>
                        Showing <strong>{{ (($page -1) * 25) + 1 }}</strong> -
                        <strong>{{ $page * 25 > $results->getPath('hits/found') ? $results->getPath('hits/found') : $page * 25 }}</strong>
                        of <strong>{{ $results->getPath('hits/found') }}</strong> Results
                    </div>
                @endif
            </div>
            <div class="col-md-3 text-right">
                <a href="#" class="btn btn-success btn-with-icon btn-download download-site">Download Results</a>
            </div>
        </div>
        <table class="table blue-colored-table sort-table">
            <thead>
            <tr>
                <th><a href="#">Title <span class="glyphicon glyphicon-sort-by-attributes"></span></a></th>
                <th>Description</th>
                <th><a href="#">Type <span class="glyphicon glyphicon-sort"></span></a></th>
                <th>Community</th>
                <th>Date</th>
                @if(is_super_admin())
                    <th>Action</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @if($results->getPath('hits/found') > 0)
                @foreach($results->getPath('hits/hit') AS $key => $entry)
                    <tr>
                        <td class="col-md-3">
                            <a href="{{ $entry['fields']['link'][0] }}" target="_blank">
                                @if($entry['fields']['post_type'][0] == 'Test Scenario')
                                    {!! $entry['fields']['post_title'][0] . '<br>( ' . \App\Post::find($entry['fields']['post_id'][0])->post_title . ' )' !!}
                                @else
                                    {!! $entry['fields']['post_title'][0] !!}
                                @endif
                            </a>
                        </td>
                        <td>
                            {{ \Illuminate\Support\Str::limit(strip_tags(@$entry['fields']['post_content'][0]), 400) }}
                        </td>
                        <td class="text-center col-md-1">
                            {{ $entry['fields']['post_type'][0] }}
                        </td>
                        <td class="text-center col-md-1">
                            {{ implode(',', $entry['fields']['community']) }}
                        </td>
                        <td class="text-center col-md-1">
                            {{ formatDate($entry['fields']['last_updated_date'][0], 'Y-m-d') }}
                        </td>
                        @if(is_super_admin())
                            <td class="text-center">
                                <a href="#" data-toggle="modal" data-target="#deleteEntryModal" class="btn btn-icon btn-danger btn-delete delete_search_entry"
                                   data-id="{{ $entry['id'] }}">
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ is_super_admin() ? 6 : 5 }}" class="text-center">No data found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $paginator->links() }}
</div>