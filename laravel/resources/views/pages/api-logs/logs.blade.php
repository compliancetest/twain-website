<div class="table-responsive">
    <div class="log-results-table-wrapper">
        <table class="table colored-parent-table log-results-table">
            <thead>
            <tr>
                <th>User</th>
                <th>Request Type</th>
                <th>URI</th>
                <th>Request<br>Response</th>
                <th>Date<br/>Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs AS $log)
                <tr>
                    <td class="text-center">
                        {{ cp_get_user_fullname($log->user_id) }}
                    </td>
                    <td class="text-center">
                        {{ $log->request_type }}
                    </td>
                    <td class="text-center">
                        {{ $log->uri }}
                    </td>
                    <td class="text-center">
                        @if($log->request == '[]')
                            -
                        @else
                            <a href="/api-logs/{{ $log->id }}/request" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewLogModal"
                               data-tooltip="tooltip" title="View Request">View Request</a>
                        @endif
                        <br>
                        <a href="/api-logs/{{ $log->id }}/response" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewLogModal"
                           data-tooltip="tooltip" title="View Response">View Response</a>
                    </td>
                    <td class="text-center">
                        {{ formatDate($log->updated_at, 'Y-m-d') }}
                        <br>
                        {{ formatDate($log->updated_at, 'H:i:s') }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $logs->appends($_GET)->render() }}
</div>