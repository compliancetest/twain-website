{{ Form::model($download, ['file' => true, 'method' => 'PATCH', 'class' => 'file-edit-form', 'url' => '/downloads/' . $community->slug . '/'. $download->id]) }}

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
            <a href="#" class="btn btn-success btn-with-icon btn-upload" id="save_new_file"
               onclick="submitDownload('.file-edit-form')">Upload &amp; Save</a>
            <a href="#" class="btn btn-default btn-with-icon btn-cancel" onclick="jQuery('#edit-download-section').html('')"
               id="cancel-add-new-files">Cancel</a>
        </div>
    </div>
{{ Form::close() }}