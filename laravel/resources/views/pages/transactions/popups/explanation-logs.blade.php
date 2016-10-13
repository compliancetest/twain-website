<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Messages
</div>
<div class="modal-body">
    <div class="alert alert-success text-center" role="alert">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
          Please feel free to ask a question about the test result, we will be happy to help you.
    </div>

    @if(!$logs->isEmpty())
        <ul class="chat-list">
        @foreach($logs as $log)
            <li class="{{ ($log->is_support) ? 'support-answer' : 'customer-question' }}">
                <div class="message-author"><a href="/members/{{ \App\User::find($log->user_id)->user_nicename }}" target="_blank">{{ cp_get_user_fullname($log->user_id) }}</a></div>
                <div class="message-body">
                    {{ $log->message }}
                </div>
                <div class="message-date">{{ dateDiffForHumans($log->created_at, 'Y-m-d H:i') }}</div>
            </li>
        @endforeach
        </ul>
    @else
         <div class="text-center">
            No messages yet
        </div>
    @endif

    <form>
        <div class="form-group">
            <input type="hidden" id="transactionId" value="{{ $transactionId }}">
            <textarea class="form-control" id="explanationMessage" name="explanationMessage"></textarea>
        </div>
    </form>


</div>
<div class="modal-footer">
    @if(!($isSupport && $logs->isEmpty()))
        <button type="submit" class="btn btn-success submit-new-message">Submit</button>
    @endif
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>