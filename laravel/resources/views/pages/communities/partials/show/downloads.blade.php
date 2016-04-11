<div class="community-tab-content">

    <div class="community-downloads row">
        <div class="col-md-12 table-responsive">
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
    </div>
    <div class="add-new-download-section">
        @if($isAdmin)
            <div class="add-new-download-default">
                <a href="#add-new-download-section" id="add-new-download" class="add-new-download-link">Upload New
                    File(s)</a>
            </div>
        @endif
        <div id="edit-download-section"></div>
        <div id="add-new-download-section" style="display: none;">

            {{ Form::open(['file' => true, 'id' => 'newfileform', 'url' => '/downloads/' . $community->slug, 'data-validate' => 'validate'] ) }}
            <h3>Upload New File(s)</h3>

            <div class="file-description-section">
                <div class="upload-file-field">
                    <input type="file" name="file" class="input-file"/>
                </div>

                <div class="file-description-fields">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">File Version:</label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="version" value=""/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description:</label>

                            <div class="col-sm-9">
                                    <textarea cols="20" rows="5" name="description"
                                              class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">File License Agreement:</label>

                            <div class="col-sm-9">
                                <textarea cols="20" rows="5" name="license" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="btn btn-danger btn-with-icon btn-delete">Remove</a>
            </div>
            <div class="form-actions">

                <div class="pull-right">
                    <a href="#" class="btn btn-success btn-with-icon btn-upload" id="save_new_file"
                       onclick="submitDownload('#newfileform')">Upload &amp; Save</a>
                    <a href="#" class="btn btn-default btn-with-icon btn-cancel"
                       id="cancel-add-new-files">Cancel</a>
                </div>
            </div>
            {{ Form::close() }}
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
                jQuery('#edit-download-section').show().html(data)
            });
        });

        @endif

    });
    function submitDownload(form) {
        var formData = jQuery(form).serialize();
        jQuery('.message').hide();
        jQuery(form).ajaxSubmit({
            data: formData,
            dataType: 'json',
            success: function (data) {
                location.href = data.redirect_to;
            },
            error: function (data) {
                var errors = data.responseJSON;
                jQuery('.message').html('').show();
                jQuery.each(errors, function (index, value) {
                    jQuery('.message').append(value)
                });
            }
        });
        return false;
    }
</script>
