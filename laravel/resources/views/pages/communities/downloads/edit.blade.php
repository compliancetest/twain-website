{{ Form::model($download, ['enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'file-edit-form','id' => 'file-edit-form', 'url' => getSiteUrl() . '/downloads/' . $community->slug . '/'. $download->id]) }}

<h3>Edit File</h3>

<div class="file-description-section">
    <div class="upload-file-field">
        <input type="file" name="file" class="input-file"/>
    </div>

    <div class="file-description-fields">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-3 control-label">File Name:</label>

                <div class="col-sm-9">
                    {!! Form::text('title', null, ['readonly' => 'readonly', 'class' => 'form-control readonly']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">File Version:</label>

                <div class="col-sm-9">
                    {!! Form::text('version', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Description:</label>

                <div class="col-sm-9">
                    {!! Form::textarea('description', null, ['cols' => '20', 'rows' => 5, 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Description of Changes:</label>

                <div class="col-sm-9">
                    {!! Form::text('version_description', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">File License Agreement:</label>

                <div class="col-sm-9">
                    {!! Form::textarea('license', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>

</div>
<div class="form-actions">
    <div class="pull-right">
        <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_new_file">Upload &amp; Save</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" onclick="jQuery('#edit-download-section').html('')" id="cancel-add-new-files">Cancel</button>
    </div>
</div>
{{ Form::close() }}

<script>
    $('#file-edit-form').submit(function (e) {
        var form = $(this);

        if (form.valid()) {
            e.preventDefault();

            jQuery('#uploadFileSpinner').show();
            var formData = jQuery(form).serialize();
            jQuery('.error-message').hide();
            jQuery(form).ajaxSubmit({
                data: formData,
                dataType: 'json',
                success: function (data) {
                    jQuery('#uploadFileSpinner').hide();
                    location.href = data.redirect_to;
                },
                error: function (data) {
                    jQuery('#uploadFileSpinner').hide();
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