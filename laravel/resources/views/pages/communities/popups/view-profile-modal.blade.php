{{-- View Profile Modal--}}
<div class="modal fade profile-modal" id="modalViewProfile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                Profile Instance Type Detail
            </div>
            <div class="modal-body">
                <div class="block-loading">
                    <div class="loading-content"><span class="loader"></span>
                        <div class="loading-text">LOADING PROFILE</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-success btn-with-icon btn-download">Download</a>
                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>
<script>
    $('#modalViewProfile').on('hidden.bs.modal', function (e) {
        $(this).find('.modal-body').html('<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
    });
</script>