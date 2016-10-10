<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Image Viewer
</div>
<div class="modal-body">
    <div class="flexslider">
        <ul class="slides">
            <?php $scannedImagesData = $transaction->getScannedImagesData();?>
            @foreach($scannedImagesData AS $k => $scannedImageData)
                {{--If both images available use this layout--}}
                <li>
                    @if($scannedImageData['expectedImage'])
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Scanned Image</h4>
                                <a target="_blank" href="{{ $scannedImageData['image'] }}">
                                    <img src="{{ $scannedImageData['image'] }}"/>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <h4>Expected Image</h4>
                                <a target="_blank" href="{{ $scannedImageData['expectedImage'] }}">
                                    <img src="{{ $scannedImageData['expectedImage'] }}"/>
                                </a>
                            </div>
                            <div class="col-md-4">
                                @include('pages.my.verify_requests._image_viewer_conditions_form', ['imageNumber' => $k])
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Scanned Image</h4>
                                <a target="_blank" href="{{ $scannedImageData['image'] }}">
                                    <img src="{{ $scannedImageData['image'] }}"/></a>
                            </div>
                            <div class="col-md-6">
                                @include('pages.my.verify_requests._image_viewer_conditions_form')
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
            <li>
                <div class="row">
                    <div class="col-md-12">
                        @foreach($scannedImagesData as $k => $scannedImageData)
                            <h3>Image #{{ $k+1 }}:</h3>
                            <div class="form-group">
                                @include('pages.my.verify_requests._image_viewer_conditions_form', ['readonly' => true, 'imageNumber' => $k])
                            </div>
                        @endforeach
                        <div class="alert alert-warning">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            Please check Pass and Skip conditions on the previous page(s) or provide a reason for Fail / Skip if none of them is met.
                        </div>
                        <button class="verify_as_pass btn btn-success btn-with-icon btn-trigger change_status" data-outcome="Pass" style="display: none;">Verify As Pass</button>
                        <button class="verify_as_fail btn btn-danger btn-with-icon btn-trigger change_status" data-outcome="Fail" style="display: none;">Verify As Fail</button>
                        <button class="verify_as_skip btn btn-default btn-with-icon btn-trigger change_status" data-outcome="Skip" style="display: none;">Verify As Skip</button>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Close</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        $('.flexslider').flexslider({
            slideshow: false,
            multipleKeyboard: true
        });

        $('.skipConditions').on('change', function (e) {
            if ($(this).is(':checked')) {
                $(this).addClass('checked');
            } else {
                $(this).removeClass('checked');
            }
            if ($('.skipConditions[data-image="' + $(this).data('image') + '"].checked').length > 0) {
                $('.passConditions[data-image="' + $(this).data('image') + '"]').prop('checked', false).removeClass('checked');
            } else {
                $(this).closest('form').find('.passConditions[data-image="' + $(this).data('image') + '"]');
            }
            $('.skipConditions[data-image="' + $(this).data('image') + '"][value="' + $(this).val() + '"]').prop('checked', $(this).is(':checked'));
            checkButtons();
        });
        $('.passConditions').on('change', function (e) {
            if ($(this).is(':checked')) {
                $(this).addClass('checked');
            } else {
                $(this).removeClass('checked');
            }
            if ($('.passConditions[data-image="' + $(this).data('image') + '"].checked').length > 0) {
                $('.skipConditions[data-image="' + $(this).data('image') + '"]').prop('checked', false).removeClass('checked');
                $('.reason[data-image="' + $(this).data('image') + '"]').val('').attr('disabled', 'disabled');
            } else {
                $(this).closest('form').find('.skipConditions[data-image="' + $(this).data('image') + '"]');
                $('.reason[data-image="' + $(this).data('image') + '"]').removeAttr('disabled');
            }
            $('.passConditions[data-image="' + $(this).data('image') + '"][value="' + $(this).val() + '"]').prop('checked', $(this).is(':checked'));
            checkButtons();
        });
        $('.reason').on('change', function () {
            $('.reason[data-image="' + $(this).data('image') + '"]').val($(this).val());
            checkButtons();
        });

        function checkButtons() {
             var notEmptyReason = $('.reason').filter(function () {
                return this.value.length !== 0;
            });
            if ($('.passConditions:checked').length == $('.passConditions').length) {
                $('.viewImagesModal .verify_as_pass').show();
            } else {
                $('.viewImagesModal .verify_as_pass').hide();
            }
            if ($('.skipConditions:checked').length > 0 || notEmptyReason.length > 0) {
                $('.viewImagesModal .verify_as_skip').show();
            } else {
                $('.viewImagesModal .verify_as_skip').hide();
            }
            if (notEmptyReason.length > 0) {
                $('.viewImagesModal .verify_as_fail').show();
            } else {
                $('.viewImagesModal .verify_as_fail').hide();
            }
        }

        $('.viewImagesModal .change_status').on('click', function (e) {
            jQuery('.viewImagesModal .block-loading').show();
            var outcomeType = $(this).data('outcome');
            var reason = '';
            var notEmptyReason = $('.reason').filter(function () {
                return this.value.length !== 0;
            });
            if (outcomeType == 'Skip') {
                reason = notEmptyReason.length ? notEmptyReason.val() : 'Skip condition "'+$('.skipConditions.checked:first').val()+'" for the image #'+(parseInt($('.skipConditions.checked:first').data('image')) + 1)+' was not met.';
            } else if (outcomeType == 'Fail') {
                reason = notEmptyReason.length ? notEmptyReason.val() : 'Pass condition "'+$('.passConditions:not(.checked):first').val()+'" for the image #'+(parseInt($('.passConditions:not(.checked):first').data('image')) + 1)+' was not met.';
            }

            jQuery.ajax({
                url: '/verify-requests/{{ $communitySlug }}/update-image-transaction/{{ $verifyRequest->id }}/{{ $transaction->id }}',
                data: {
                    'outcome_code': outcomeType,
                    'reason': reason,
                    'hideResolved': $('#hideResolved:checked').length,
                    'hideOthers': $('#hideOthers:checked').length,
                },
                type: 'post',
                dataType: 'json',
                success: function (rsp) {
                    jQuery('.viewImagesModal .block-loading').hide();
                    $('#verifyRequestsListContent').html(rsp.html);
                    $('.viewImagesModal .modal-body').prepend('<div class="success-message">Test result was updated successfully!</div>');
                    setTimeout(function () {
                        $('.modal').modal('hide');
                    }, 3000);

                },
                error: function (jqXHR, status) {
                    jQuery('.viewImagesModal .block-loading').hide();
                    $('.viewImagesModal .modal-body').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    setTimeout(function () {
                        $('.viewImagesModal .modal-body > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 3000);
                }
            });
        });
    });
</script>