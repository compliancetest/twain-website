<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Delete Article
</div>
<div class="modal-body">
    Are you sure you want delete "{{ $article->title }}"?
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success btn-with-icon btn-confirm delete-article">Confirm</button>
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>

<script>
    jQuery('.delete-article').click(function (e) {
        e.preventDefault();
        $('#deleteArticleModal .block-loading').show();
        jQuery.ajax({
            url: '/articles/{{ $community->slug }}/{{ $article->slug }}',
            type: 'delete',
            dataType: 'json',
            success: function (rsp) {
                $('#deleteArticleModal .block-loading').hide();
                $('#deleteArticleModal .modal-body').append('<div class="success-message">Article has been deleted successfully!</div>');
                setTimeout(function () {
                    $('.modal').modal('hide');
                    var elem = $(".btn-delete[data-article-id='{{ $article->id }}']");
                    $(elem).closest('tr').slideUp('slow', function () {
                        $(elem).remove();
                    });
                }, 2000);
            },
            error: function (jqXHR, status) {
                $('#deleteArticleModal .block-loading').hide();
                $('#deleteArticleModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                setTimeout(function () {
                    $('#deleteArticleModal .modal-body > .error-message').slideUp(function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    });
    $('#deleteArticleModal').on('hidden.bs.modal', function (e) {
        var popupLoadingBlock = '<div class="modal-header">' +
                '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                'Delete Article' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                '</div>';
        $(this).find('.modal-content').html(popupLoadingBlock);
    });
</script>