<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Delete Download
</div>
<div class="modal-body">
    Are you sure you want delete "{{ $download->title }}"?
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success btn-with-icon btn-confirm delete-download">Confirm</button>
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>

<script>
    jQuery('.delete-download').click(function (e) {
        e.preventDefault();
        $('#deleteDownloadModal .block-loading').show();
        jQuery.ajax({
            url: '/downloads/{{ $community->slug }}/{{ $download->id }}',
            type: 'delete',
            dataType: 'json',
            success: function (rsp) {
                $('#deleteDownloadModal .block-loading').hide();
                $('#deleteDownloadModal .modal-body').append('<div class="success-message">Download has been deleted successfully!</div>');
                $('#edit-download-section').html('');
                setTimeout(function () {
                    $('.modal').modal('hide');
                    var elem = $(".btn-delete[data-download-id='{{ $download->id }}']");
                    $(elem).closest('tr').slideUp('slow', function () {
                        $(elem).remove();
                        if (!$('.downloads-list-table tbody tr').length) {
                            $('.downloads-list-table tbody').html('<tr><td colspan="@if($isAdmin) 6 @else 5 @endif" class="empty-row">No files uploaded yet</td></tr>');
                        }
                    });
                }, 2000);
            },
            error: function (jqXHR, status) {
                $('#deleteDownloadModal .block-loading').hide();
                $('#deleteDownloadModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                setTimeout(function () {
                    $('#deleteDownloadModal .modal-body > .error-message').slideUp(function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    });
    $('#deleteDownloadModal').on('hidden.bs.modal', function (e) {
        var popupLoadingBlock = '<div class="modal-header">' +
                '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                'Delete Download' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                '</div>';
        $(this).find('.modal-content').html(popupLoadingBlock);
    });
</script>