<div class="filter-list-actions">
    <div class="col-md-9">
        <div class="filter-results-count">
            @if(count($entries))
                <div class="filter-results-count">
                    <?php $page = $entries->currentPage();?>
                    Showing <strong>{{ (($page -1 ) * 25) + 1 }}</strong> -
                    <strong>{{ $page == $entries->lastpage() ? $entries->total() : $page * 25 }}</strong>
                    of <strong>{{ $entries->total() }}</strong> Results
                </div>
            @endif
        </div>
    </div>
</div>
<div class="table-responsive">
    <div class="blue-colored-table-wrapper">

        <table class="table blue-colored-table sort-table test-suites-table">
            <thead>
            <tr>
                <th><a href="#" class="sortby" data-type="full_name" data-order="{{ getSortingOrder($request, 'full_name') }}">Name <span
                                class="glyphicon {{ getSortingCssClass($request, 'full_name') }}"></span></a></th>
                <th>Community</th>
                <th>Issuer</th>
                <th>Published</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @if(count($entries))
                @foreach($entries as $entry)
                    <tr>
                        <td><a href="/test-suite/{{ $entry->slug }}">{{ $entry->full_name }}</a></td>
                        <td class="text-center"><a href="/communities/{{ $entry->communitySlug }}">{{ $entry->communityTitle }}</a></td>
                        <td class="text-center">{{ $entry->issuer }}</td>
                        <td class="text-center">{{ $entry->published_at }}</td>
                        <td class="text-center"><span class="status status-{{ strtolower($entry->status) }}">{{ $entry->status }}</span></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center">No Test Suites found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    <div class="pagination-wrapper">
        {{ $entries->links() }}
    </div>
</div>