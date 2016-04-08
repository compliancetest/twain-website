{{ Form::model($download, ['file' => true, 'method' => 'PATCH', 'class' => 'file-edit-form', 'url' => '/downloads/' . $community->slug . '/'. $download->id]) }}

        <h3>Edit File</h3>

        <div class="grid-list">
            <div class="grid-list-row">
                <div class="grid-list-cell left15 grid-field-cell">

                    {!! Form::label('title', 'File Name:') !!}
                    {!! Form::text('title', null, ['readonly' => 'readonly', 'class' => 'text readonly']) !!}

                    <br clear="all">

                    {!! Form::label('version', 'File Version:') !!}
                    {!! Form::text('version', null) !!}

                    <br clear="all">

                </div>

                <div class="grid-list-cell width35P">
                    <span class="custom-file-tag">
                        <span class="file-label-wrap file-label-oneline">
                            <span class="file-value">Choose File</span>
                        </span>
                        <span class="action-btn file-btn">
                        <input name="file" class="input-file" type="file">
                            <span class="p"></span>
                            <span class="t">Browse</span>
                        </span>
                    </span>

                    <br clear="all">(The original file will be replaced. Please leave this blank if you don't want change the file.)

                </div>

                <div class="clear"></div>

                <div class="grid-list-cell grid-field-cell width85P">

                    {!! Form::label('description', 'Description: ') !!}

                    <div class="redactor_box">

                        <div style="min-height: 80px;" dir="ltr" class="redactor_text redactor_editor redactor_description" contenteditable="true">{!! $download->description !!}​</div>

                        {!! Form::textarea('description', null, ['cols' => '20', 'rows' => 5, 'class' => 'text description_data', 'style' => 'display:none;', 'dir' => 'ltr']) !!}

                    </div>

                    <br clear="all">

                    {!! Form::label('version_description', 'Description of Changes:') !!}
                    {!! Form::text('version_description', null, ['class' => 'text file_changes_desc']) !!}

                    <br clear="all">

                    {!! Form::label('license', 'File License Agreement: ') !!}
                    <div class="redactor_box">

                        <div style="min-height: 80px;" dir="ltr" class="redactor_textarea redactor_editor redactor_license" contenteditable="true">{!! $download->license !!}​</div>

                        {!! Form::textarea('license', null, ['cols' => '20', 'rows' => 5, 'class' => 'textarea license_data', 'style' => 'display:none;', 'dir' => 'ltr']) !!}

                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="grid-list-footer grid-list-row">

                <a href="#" class="action-btn cancel-btn cancel-file-edit">
                    <span class="p"></span><span class="t">Cancel</span>
                </a>
                <a href="javascript: void(0)" class="action-btn process-btn save-file-edit">
                    <span class="p"></span><span class="t">SAVE</span>
                </a>
                <div class="clear"></div>
                <div class="message" style="display: none;"></div>
            </div>
        </div>


{{ Form::close() }}

<script>
    jQuery('.redactor_editor').redactor({
        air: true,
        minHeight: 80
    })
    jQuery('.cancel-file-edit').on('click', function(){
        jQuery(this).closest('form').remove();
    });

    jQuery('.save-file-edit').on('click', function(){
        jQuery(this).closest('form').submit();
    });

    jQuery(jQuery('.save-file-edit').closest('form')).on('submit', function(){

        jQuery('.license_data').val(jQuery('.redactor_license').html());

        jQuery('.description_data').val(jQuery('.redactor_description').html());

        var formData = jQuery(this).serialize();

        jQuery('.message').hide();

        jQuery(this).ajaxSubmit({
            data: formData,
            dataType: 'json',
            success: function(data){
                location.href = data.redirect_to;
            },
            error: function(data){
                var errors = data.responseJSON;
                jQuery('.message').html('').show();
                jQuery.each(errors, function(index, value){
                    jQuery('.message').append(value)
                });
            }
        });
        return false;
    });

</script>