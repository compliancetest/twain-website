{{ Form::open(['file' => true, 'id' => 'newfileform', 'url' => '/downloads/' . $community->slug]) }}

    <h3>Upload New File(s)</h3>

    <div class="grid-list">
        <div class="grid-list-row">

            <div class="grid-list-cell left15 grid-field-cell">

                {!! Form::label('version', 'File Version:') !!}
                {!! Form::text('version', null) !!}

            </div>

            <div class="grid-list-cell width35P">
                {!! Form::file('file', null) !!}
            </div>
            <div class="clear"></div>
            <div class="grid-field-cell grid-list-cell width85P">

                {!! Form::label('description', 'Description: ') !!}

                <div class="redactor_box">

                    <div style="min-height: 80px;" dir="ltr" class="redactor_text redactor_editor redactor_description" contenteditable="true"></div>

                    {!! Form::textarea('description', null, ['cols' => '20', 'rows' => 5, 'class' => 'text description_data', 'style' => 'display:none;', 'dir' => 'ltr']) !!}

                </div>

                <br clear="all">

                {!! Form::label('license', 'File License Agreement: ') !!}

                <div class="redactor_box">

                    <div style="min-height: 80px;" dir="ltr" class="redactor_textarea redactor_editor redactor_license" contenteditable="true"></div>

                    {!! Form::textarea('license', null, ['cols' => '20', 'rows' => 5, 'class' => 'textarea license_data', 'style' => 'display:none;', 'dir' => 'ltr']) !!}

                </div>

            </div>
            <div class="clear"></div>
        </div>
        <div class="grid-list-footer grid-list-row">
            <a href="#" class="action-btn cancel-btn" id="cancel-file-download"><span class="p"></span><span
                        class="t">Cancel</span></a>
            <a href="#" class="action-btn upload-btn" id="save-file-download"><span class="p"></span><span class="t">Upload &amp; Save</span></a>

            <div class="clear"></div>
            <div class="message error" style="display: none;"></div>

        </div>
    </div>


{{ Form::close() }}

<script>
    jQuery('.redactor_editor').redactor({
        air: true,
        minHeight: 80
    })
    jQuery('#cancel-file-download').on('click', function(){
        jQuery(this).closest('form').remove();
        jQuery('#add-new-download').closest('div.grid-list-row').show();
    });

    jQuery('#save-file-download').on('click', function(){
        jQuery('#save-file-download').closest('form').submit();
    });

    jQuery(jQuery('#save-file-download').closest('form')).on('submit', function(){

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