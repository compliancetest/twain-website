<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Delete Test Suite
</div>
<div class="modal-body">
    Are you sure you want delete "{{ $testSuite->full_name }}"?
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success btn-with-icon btn-confirm delete-suite">Confirm</button>
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>

<script>
    jQuery('.delete-suite').click(function (e) {
        e.preventDefault();
        jQuery.ajax({
            url: '/test-suite/{{ $testSuite->slug }}',
            type: 'delete',
            dataType: 'json',
            success: function (rsp) {
                $('#deleteTestSuiteModal .block-loading').hide();
                $('#deleteTestSuiteModal .modal-body').append('<div class="success-message">Test Suite has been deleted successfully!</div>');
                setTimeout(function () {
                    $('.modal').modal('hide');
                    location.reload();
                }, 2000);
            },
            error: function (jqXHR, status) {
                $('#deleteTestSuiteModal .block-loading').hide();
                $('#deleteTestSuiteModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                setTimeout(function () {
                    $('#deleteTestSuiteModal .modal-body > .error-message').slideUp(function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    });
    $('#deleteTestSuiteModal').on('hidden.bs.modal', function (e) {
        var popupLoadingBlock = '<div class="modal-header">' +
                '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                'Delete Test Suite' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                '</div>';
        $(this).find('.modal-content').html(popupLoadingBlock);
    });
</script>