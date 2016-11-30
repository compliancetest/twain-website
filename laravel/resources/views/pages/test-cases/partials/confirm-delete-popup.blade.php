<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Delete Test Case
</div>
<div class="modal-body">
    @if(count($testCase->testSuites))
        <div class="alert alert-danger">
            Warning! This test case is currently included in the following test suites:
            @foreach($testCase->testSuites as $testSuite)
                <a href="/test-suite/{{ $testSuite->slug }}" target="_blank">{{ $testSuite->full_name }}</a><br>
            @endforeach
            Deleting the test case will remove it from all test suites. Do you wish to proceed?
        </div>
    @else
        Are you sure you want delete "{{ $testCase->full_name }}"?
    @endif
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success btn-with-icon btn-confirm delete-case">Confirm</button>
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>

<script>
    jQuery('.delete-case').click(function (e) {
        e.preventDefault();
        jQuery.ajax({
            url: '/test-case/{{ $testCase->slug }}@if($notRedirect){{ '/not_redirect' }}@endif',
            type: 'delete',
            dataType: 'json',
            success: function (rsp) {
                $('#deleteTestCaseModal .block-loading').hide();
                $('#deleteTestCaseModal .modal-body').append('<div class="success-message">Test Case has been deleted successfully!</div>');
                setTimeout(function () {
                    $('.modal').modal('hide');
                    if (rsp.redirect_to) {
                        location.href = '/test-suites/'
                    } else {
                        location.reload();
                    }
                }, 2000);
            },
            error: function (jqXHR, status) {
                $('#deleteTestCaseModal .block-loading').hide();
                $('#deleteTestCaseModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                setTimeout(function () {
                    $('#deleteTestCaseModal .modal-body > .error-message').slideUp(function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    });
    $('#deleteTestCaseModal').on('hidden.bs.modal', function (e) {
        var popupLoadingBlock = '<div class="modal-header">' +
                '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                'Delete Test Case' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                '</div>';
        $(this).find('.modal-content').html(popupLoadingBlock);
    });
</script>