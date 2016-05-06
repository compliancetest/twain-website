{{ Form::model($thread, ['file' => true, 'id' => 'editthreadform', 'url' => getSiteUrl() . '/forums/' . $community->slug .'/' . $thread->id, 'data-validate' => 'validate', 'method' => 'PATCH'] ) }}
    <h3>Edit Thread</h3>

    <div class="error-message" style="display: none;"></div>
    <div class="file-description-section">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="threadTitle">Title:</label>

                <div class="col-sm-9">
                    {!! Form::text('title', null, ['id' => 'threadTitle', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="threadDescription">Description:</label>

                <div class="col-sm-9">
                    {!! Form::textarea('content', null, ['id' => 'threadDescription', 'class' => 'form-control', 'cols' => '20', 'rows' => 5]) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions clearfix">

        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_new_thread">Save</button>
            <button type="button" class="btn btn-default btn-with-icon btn-cancel" id="cancel-edit-thread">Cancel</button>
        </div>
    </div>
{{ Form::close() }}

<script>
    $('#cancel-edit-thread').on('click', function(){
        $('#edit-thread-section').hide();
        $('#add-new-thread-section').hide();
        $('.add-new-item-default').show();
    });

    $('#editthreadform').submit(function (e) {
        var form = $(this);

        if (form.valid()) {
            e.preventDefault();

            jQuery('#addThreadSpinner').show();
            var formData = jQuery(form).serialize();
            jQuery('.error-message').hide();
            jQuery(form).ajaxSubmit({
                data: formData,
                dataType: 'json',
                success: function (data) {
                    jQuery('#addThreadSpinner').hide();
                    location.href = data.redirect_to;
                },
                error: function (data) {
                    jQuery('#addThreadSpinner').hide();
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