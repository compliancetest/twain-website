<div class="table">
    <div class="thead tr">
        <div class="td td-from td-two-lines tocenter" style="width: 4%;">
            From</br>To
        </div>

        <div class="td td-service td-two-lines tocenter" style="width: 4%;">
            Test </br>
            Step
        </div>

        <div class="td td-service td-two-lines tocenter"
             style="width: 36%;">Operation Triplet </br>
            Return Code
        </div>
        <div class="td td-message-date td-two-lines" style="width: 4%;">Session State
        </div>
        <div class="td td-message-part tocenter td-two-lines" style="width: 7%;">Message
            Data
        </div>
        <div class="td td-message-view td-two-lines" style="width: 7%;">Date</br>Time</div>
        <div class="td td-message-view td-two-lines" style="width: 8%;">Step Outcome</div>
        <div class="td td-service td-two-lines tocenter" style="width: 9%;">
            Screen </br>
            Capture
        </div>
        <div class="td td-service td-two-lines tocenter" style="width: 9%;">
            Scan </br>
            Result
        </div>
        <div class="clear"></div>
    </div>
    <div class="tbody">
        @if ($logs)
            @foreach ($logs as $message)
                <div class="tr">
                    <div class="td td-from td-two-lines tocenter" style="width: 4%;">
                        {{ $message->from }}
                        </br>
                        {{ $message->to }}
                    </div>
                    <div class="td td-service td-two-lines tocenter" style="width: 4%;">
                        @if(!empty($message->test_step))
                            <a href="/test-case/{{ $testCase->post_name }}#step_anchor_{{ $message->test_step }}" target="_blank">{{ $message->test_step }}</a>
                        @endif
                    </div>
                    <div class="td td-service td-two-lines tocenter" style="width: 36%;">
                        {{ $message->data_group }} / {{ $message->data_argument_type }} / {{ $message->messages }} </br>
                            <span style="color: {{ getReturnCodeColor($message->return_code) }}">{{ $message->return_code }}</span>
                    </div>
                    <div class="td td-message-date" style="width: 4%;">
                        @if($message->session_state)
                            {{ $message->session_state }}
                        @endif
                    </div>
                    <div class="td td-message-part tocenter" style="width: 7%;">
                        @if(!empty($message->log_output))
                            <a href="/testingdetails/{{ $message->id }}/output" class="s3output">View</a>
                        @endif
                    </div>
                    <div class="td td-message-view" style="width: 7%;">{{ $message->updated_at }}</div>
                    <div class="td td-message-view" style="width: 8%;">
                        @if(empty($message->reason))
                            <span class="status-<?php echo getOutcomeStatusClass(strtoupper($message->step_outcome));?>">{{ $message->step_outcome }}</span>
                        @else
                            <a href="/testingdetails/{{ $message->id }}/reason" class="reason">
                                <span class="status-{{ getOutcomeStatusClass(strtoupper($message->step_outcome)) }}">{{ $message->step_outcome }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                        -
                    </div>
                    <div class="td td-service td-two-lines tocenter" style="width: 9%;">
                        @if ($scanImages = json_decode($message->scan_results))
                            @foreach ($scanImages as $scanImage)
                                <a href="{{ $scanImage }}" target="_blank">View</a>
                            @endforeach
                        @else
                            -
                        @endif
                    </div>
                    <div class="clear"></div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
    jQuery('.s3output, .reason').cplightbox({
        type: 'ajax',
        closeWhenClickOveraly: false
    })
</script>