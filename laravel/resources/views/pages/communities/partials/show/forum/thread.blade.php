<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <div class="success-message hide">Download was deleted successfully!</div>

            <h1>{{ $thread->title }}</h1>

            <a href="/communities/{{ $community->slug }}/forum" class="btn btn-default btn-with-icon btn-back">Back to forums</a>

            {{ $thread->content }}

            Replies:

            <table class="table downloads-list-table">
                <thead>
                <tr>
                    <th class="text-left">Author</th>
                    <th>Message</th>
                    <th>Date</th>
                    @if($isAdmin)
                        <th>Actions</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @if(count($threadPosts))
                    @foreach($threadPosts as $threadPost)
                        <tr>
                            <td class="text-nowrap text-left">{{ $threadPost->user->getFullName() }}</td>
                            <td class="text-center">
                                {{ $threadPost->content }}
                            </td>
                            <td class="text-nowrap text-center">{{ formatDate($threadPost->updated_at) }}</td>
                            @if($isAdmin)
                                <td class="text-nowrap text-center">
                                    <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip" data-id="{{ $threadPost->id }}" title="Delete"></a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="empty-row ">No replies yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="add-new-post-section">
            <div class="add-new-download-default">
                <a href="#add-new-post-section" id="add-new-post" class="add-new-download-link">Reply</a>
            </div>
            <div id="edit-download-section"></div>
            <div id="add-new-post-section" style="display: none;">
                {{ Form::open(['file' => true, 'id' => 'newpostform', 'url' => getSiteUrl() . '/forums/' . $community->slug .'/'.$thread->slug, 'data-validate' => 'validate'] ) }}
                <h3>Add new post</h3>

                <div class="error-message" style="display: none;"></div>
                <div class="file-description-section">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="postTitle">Message:</label>

                            <div class="col-sm-9">
                                <textarea id="postTitle" class="form-control" name="content"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions clearfix">

                    <div class="pull-right">
                        <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_new_post">Save</button>
                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" id="cancel-add-new-post">Cancel</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <div class="block-loading" id="addpostSpinner">
                <div class="loading-content"><span class="loader"></span>

                    <div class="loading-text">SAVING YOUR DATA</div>
                    <div class="loading-wait">Please wait...</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {

        $('#add-new-post').on('click', function(){
            $('#add-new-post-section').show();
        });
         $('#cancel-add-new-post').on('click', function(){
            $('#add-new-post-section').hide();
        });

        $('#newpostform').submit(function (e) {
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

        jQuery('.btn-delete').on('click', function (e) {
            e.preventDefault();
            var elem = jQuery(this);
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: '/downloads/{{ $community->slug }}/' + elem.attr('data-id'),
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (result) {
                        jQuery('.success-message').removeClass('hide');
                        setTimeout(function () {
                            jQuery('.success-message').addClass('hide');
                        }, 3000);
                        $(elem).closest('tr').slideUp('slow', function () {
                            $(elem).remove();
                        });
                    }
                });
                return true;
            }
        });
    });
</script>