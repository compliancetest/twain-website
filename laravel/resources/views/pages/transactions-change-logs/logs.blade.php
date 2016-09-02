<div class="table-responsive">
    <div class="log-results-table-wrapper">
        <table class="table colored-parent-table log-results-table">
            <thead>
            <tr>
                <th>Execution Id</th>
                <th>User</th>
                <th>Test Outcome</th>
                <th>Changed By Server Validation</th>
                <th>Date<br/>Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs AS $log)
                <tr>
                     <td class="text-center">
                        {{ $log->execution_id }}
                    </td>
                    <td class="text-center">
                        <a href="/members/{{ \App\User::find($log->user_id)->user_nicename }}" target="_blank">{{ cp_get_user_fullname($log->user_id) }}</a>
                    </td>
                    <td class="text-center">
                        <?php
                            $outcomeStatus = \App\TestOutcomeStatus::find($log->test_outcome_status_id);
                            $status = getOutcomeStatusClass($outcomeStatus->code);
                        ?>
                        <span class="text-status-{{ $status }}">{{ $outcomeStatus->name }}</span>
                    </td>
                    <td class="text-center">
                        {{ $log->changed_by_server_validation ? 'Yes' : 'No' }}
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