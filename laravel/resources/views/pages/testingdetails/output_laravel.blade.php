<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Message Data
</div>
<form action="/verify-requests/{{ $testSuiteId }}/assign/{{ $verifyRequest->id }}" id="assignVerifyRequestForm" method="post">
    <div class="modal-body">
        <div id="data"></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
        <a href="{{ $link }}" target="_blank" class="btn btn-success btn-with-icon btn-confirm">Download</a>
    </div>
    <div class="block-loading">
        <div class="loading-content"><span class="loader"></span>
            <div class="loading-text">LOADING DATA</div>
            <div class="loading-wait">Please wait...</div>
        </div>
    </div>
</form>

<script>
    var t_data = Jsonary.create({!! $data !!}).readOnlyCopy();
    var t_element = document.getElementById('data');
    Jsonary.render(t_element, t_data);
</script>