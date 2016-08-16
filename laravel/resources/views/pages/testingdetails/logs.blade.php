<table class="table colored-table log-results-sub-table">
    <thead>
        <tr>
            <th>From<br/>To</th>
            <th>Test<br/> Step</th>
            <th>
                Operation Triplet<br/>
                Return Code
            </th>
            <th>Session<br/>State</th>
            <th>Message<br/> Data</th>
            <th>Date<br/>Time</th>
            <th>Step<br/>Outcome</th>
            <th>Screen<br/>Capture</th>
            <th>Scan<br/>Result</th>
        </tr>
    </thead>
    <tbody>
    @if ($logs)
    @foreach ($logs as $message)
    <tr>
        <td>{{ $message->from }}<br />{{ $message->to }}</td>
        <td>
            @if(!empty($message->test_step))
            <a href="/test-case/{{ $testCase->post_name }}#step_anchor_{{ $message->test_step }}" target="_blank">{{ $message->test_step }}</a>
            @endif
        </td>
        <td>
            {{ $message->data_group }} / {{ $message->data_argument_type }} / {{ $message->messages }} </br>
            <span style="color: {{ getReturnCodeColor($message->return_code) }}">{{ $message->return_code }}</span>
        </td>
        <td>
            @if($message->session_state)
            {{ $message->session_state }}
            @endif
        </td>
        <td>
            @if(!empty($message->log_output))
            <a href="/testingdetails/{{ $message->id }}/output" class="s3output"  data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalLogTestingDetails">View</a>
            @endif
        </td>
        <td>{{ $message->updated_at }}</td>
        <td>
            @if(empty($message->reason))
            <span class="text-status-{{ getOutcomeStatusClass(strtoupper($message->step_outcome)) }}">{{ $message->step_outcome }}</span>
            @else
            <a href="/testingdetails/{{ $message->id }}/reason" class="reason">
                <span class="text-status-{{ getOutcomeStatusClass(strtoupper($message->step_outcome)) }}">{{ $message->step_outcome }}</span>
            </a>
            @endif
        </td>
        <td>-</td>
        <td>
            @if ($scanImages = json_decode($message->scan_results))
            @foreach ($scanImages as $scanImage)
            <a href="{{ $scanImage }}" target="_blank">View</a>
            @endforeach
            @else
            -
            @endif
        </td>
    </tr>
    @endforeach

    @else
    <tr>
        <td colspan="9" class="text-center">No data</td>
    </tr>
    @endif
    </tbody>
</table>