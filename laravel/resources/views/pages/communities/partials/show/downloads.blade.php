<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <table class="table downloads-list-table">
                <thead>
                <tr>
                    <th class="text-left">File name</th>
                    <th>Size</th>
                    <th>License Agreement</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @if(count($downloads))
                    @foreach($downloads as $download)
                        <tr>
                            <td>
                                <a href="#">{{ $download->title }}</a>

                                <p>
                                    @if(!empty($download->version))
                                        Version:
                                        <strong>{{ $download->version }}</strong> @if(!empty($download->version_description))
                                            ({{ $download->version_description }}) @endif<br/>
                                    @endif
                                </p>

                                <p>{{ $download->description }}</p>
                            </td>
                            <td class="text-nowrap text-center">{{ formatBytes($download->size) }}</td>
                            <td>{{ $download->license }}</td>
                            <td class="text-nowrap text-center">{{ formatDate($download->created_at) }}</td>
                            @if($isAdmin)
                                <td class="text-nowrap text-center">
                                    <a href="#" class="btn btn-icon btn-primary btn-edit editDownload"
                                       data-tooltip="tooltip" title="Edit" data-id="{{ $download->id }}"></a>
                                    <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                                       title="Delete"></a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="empty-row ">No file uploaded yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="add-new-download-section">
        @if($isAdmin)
            <div class="add-new-download-default">
                <a href="#add-new-download-section" id="add-new-download" class="add-new-download-link">Upload New File(s)</a>
            </div>
        @endif
        <div id="edit-download-section"></div>
        <div id="add-new-download-section" style="display: none;">
            {{ Form::open(['file' => true, 'id' => 'newfileform', 'url' => '/downloads/' . $community->slug, 'data-validate' => 'validate'] ) }}
            <h3>Upload New File(s)</h3>
            <div class="error-message" style="display: none;"></div>
            <div class="file-description-section">
                <div class="upload-file-field">
                    <input type="file" name="file" class="input-file" required />
                </div>

                <div class="file-description-fields">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="fileVersion">File Version:</label>
                            <div class="col-sm-9">
                                <input type="text" id="fileVersion" class="form-control" name="version" value=""/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="fileDescription">Description:</label>
                            <div class="col-sm-9">
                                <textarea cols="20" rows="5" id="fileDescription" name="description" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="fileLicenseAgreement">File License Agreement:</label>
                            <div class="col-sm-9">
                                <textarea cols="20" rows="5" id="fileLicenseAgreement" name="license" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-with-icon btn-delete">Remove</button>
            </div>
            <div class="form-actions clearfix">

                <div class="pull-right">
                    <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_new_file">Upload &amp; Save</button>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" id="cancel-add-new-files">Cancel</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
        <div class="block-loading" id="uploadFileSpinner"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
    </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        Page.communityDownloads.init();

        @if($isAdmin)

            jQuery('.editDownload').on('click', function () {
            jQuery('#add-new-download-section').hide();
            jQuery.get('/downloads/{{ $community->slug }}/edit/' + jQuery(this).attr('data-id'), function (data) {
                jQuery('#edit-download-section').show().html(data);
                customizeFileTag();
            });
        });

        @endif

        $('#newfileform').submit(function (e) {
            var form = $(this);

            if (form.valid()){
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

    });
</script>