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
                <th><a href="#">Product <span class="glyphicon glyphicon-sort-by-attributes"></span></a></th>
                <th>Version </th>
                <th>Owner</th>
                <th><a href="#">Type <span class="glyphicon glyphicon-sort"></span></a></th>
                <th>Test Suite</th>
                <th>Role</th>
                <th>Level</th>
                <th>Status</th>
                <th><a href="#">Date <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></th>
                @if(is_super_admin())
                    <th>Action</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @if($results->getPath('hits/found') > 0)
                @foreach($results->getPath('hits/hit') AS $key => $rowEntry)
                    <?php $entry = $rowEntry['fields'];?>
                    <tr>
                        <td>
                            <a href="{{ get_permalink($entry['product_id'][0]) }}" target="_blank">
                                {{ $entry['name'][0] }}
                            </a>
                        </td>
                        <td class="text-center">
                            {{ $entry['version'][0] }}
                        </td>
                        <td class="text-center">
                            {{ $entry['owner'][0] }}
                        </td>
                        <td class="text-center">Product</td>
                        <td class="text-center">
                            <a href="/test-suite/{{ \App\Post::find($entry['suite_id'][0])->post_name }}" target="_blank">
                                {{ $entry['test_suite'][0] }}
                            </a>
                        </td>
                        <td class="text-center">
                            @if(!empty($entry['role']))
                                {{ implode(', ', $entry['role']) }}
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($entry['level']))
                                {{ implode(', ', $entry['level']) }}
                            @endif
                        </td>
                        <td class="text-center">
                            <?php if ($entry['test_type'][0] == 'Certification' && $entry['status'][0] == 'Verified' && \App\Claim::find(end(explode('_', $rowEntry['id'])))->has_exclusions == '1'): ?>
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" data-toggle="tooltip" title=""
                                  data-original-title="Some test cases were excluded/not performed during testing. Please consult the claim certificate on the product summary page for more details."></span>
                            <?php echo $entry['status'][0]; ?>
                            <?php else: ?>
                                <?php echo $entry['status'][0]; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            {{ formatDate($entry['date'][0], 'Y-m-d') }}
                        </td>
                        @if(is_super_admin())
                            <td class="text-center">
                                <a href="#" data-toggle="modal" data-target="#deleteEntryModal" class="btn btn-icon btn-danger btn-delete delete_search_entry"
                                   data-id="{{ $rowEntry['id'] }}">
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ is_super_admin() ? 10 : 9 }}" class="text-center">No data found</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $paginator->links() }}
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>