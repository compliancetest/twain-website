<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Messages
</div>
<div class="modal-body">
    <div class="row">
        @if(!$logs->isEmpty())
            @foreach($logs as $log)
                <div class="col-md-12 @if($log->is_support) text-right @endif">
                    <a href="/members/{{ \App\User::find($log->user_id)->user_nicename }}" target="_blank">{{ cp_get_user_fullname($log->user_id) }}</a><br>
                    {{ dateDiffForHumans($log->created_at, 'Y-m-d H:i') }}<br>
                    {{ $log->message }}
                </div>
            @endforeach
        @else
             <div class="col-md-12 text-center">
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
