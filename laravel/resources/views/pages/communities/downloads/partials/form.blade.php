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
            {!! Form::textarea('description', null, ['cols' => '20', 'rows' => 5]) !!}

            <br clear="all">

            {!! Form::label('license', 'File License Agreement: ') !!}
            {!! Form::textarea('license', null, ['cols' => '20', 'rows' => 5]) !!}

        </div>
        <div class="clear"></div>
    </div>
    <div class="grid-list-footer grid-list-row">
        <a href="#" class="action-btn cancel-btn" onclick="jQuery(this).parent('form').remove();"><span class="p"></span><span
                    class="t">Cancel</span></a>
        <a href="#" class="action-btn upload-btn" id="save-download"><span class="p"></span><span class="t">Upload &amp; Save</span></a>

        <div class="clear"></div>
        <div class="message" style="display: none;"></div>

    </div>
</div>