{{ Form::model($post, ['file' => true, 'id' => 'editpostform', 'url' => getSiteUrl() . '/forums/' . $community->slug .'/post/'.$post->id, 'data-validate' => 'validate', 'method' => 'PATCH'] ) }}
    <h3>Edit post</h3>

    <div class="error-message" style="display: none;"></div>
    <div class="file-description-section">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="postTitle">Message:</label>

                <div class="col-sm-9">
                    {!! Form::textarea('content', null, ['id' => 'postTitle', 'class' => 'form-control', 'cols' => '20', 'rows' => 4]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions clearfix">

        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_edit_post">Save</button>
            <button type="button" class="btn btn-default btn-with-icon btn-cancel" id="cancel-edit-new-post">Cancel</button>
        </div>
    </div>
{{ Form::close() }}

<script>
     $('#cancel-edit-new-post').on('click', function(){
        $('#edit-post-section').hide();
        $('#add-new-post-section').hide();
        $('.add-new-item-default').show();
    });
    $('#editpostform').submit(function (e) {
        var form = $(this);

        if (form.valid()) {
            e.preventDefault();

            jQuery('#addpostSpinner').show();
            var formData = jQuery(form).serialize();
            jQuery('.error-message').hide();
            jQuery(form).ajaxSubmit({
                data: formData,
                dataType: 'json',
                success: function (data) {
                    jQuery('#addpostSpinner').hide();
                    location.href = data.redirect_to;
                },
                error: function (data) {
                    jQuery('#addpostSpinner').hide();
                    var errors = data.responseJSON;
                    jQuery('.error-message').html('').show();
                    jQuery.each(errors, function (index, value) {
                        jQuery('.error-message').append(value)
                    });
                }
            });

        }
    })
</script>