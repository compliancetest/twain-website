<div class="block-loading-wrapper">
    <div class="community-downloads">
        <div class="table-responsive">
            <div class="success-message hide">Thread was deleted successfully!</div>
            <table class="table downloads-list-table">
                <thead>
                    <tr>
                        <th class="text-left">Topic name</th>
                        <th>Author</th>
                        <th>Posts</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @if(count($threads))
                    @foreach($threads as $thread)
                        <tr>
                            <td>
                                <a href="/communities/{{ $community->slug }}/forum/{{ $thread->slug }}">{{ $thread->title }}</a>
                                <p>{{ $thread->content }}</p>
                            </td>
                            <td class="text-nowrap text-center">{{ $thread->user->getFullName() }}</td>
                            <td class="text-center">
                                {{ count($thread->replies) }}
                            </td>
                            <td class="text-nowrap text-center">{{ dateDiffForHumans($thread->updated_at, 'Y-m-d H:i') }}</td>
                                <td class="text-nowrap text-center">
                                    @if($isAdmin || (Auth::check() && $thread->author_id == Auth::user()->ID))
                                        <a href="#" class="btn btn-icon btn-primary btn-edit editThread" data-tooltip="tooltip" title="Edit" data-id="{{ $thread->id }}"></a>
                                    @endif
                                    @if($isAdmin)
                                        <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip" data-id="{{ $thread->id }}" title="Delete"></a>
                                    @endif
                                </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="empty-row ">No threads yet</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        <div class="add-new-item-section">
            <div class="add-new-item-default">
                <a href="#add-new-thread-section" id="add-new-thread" class="add-new-download-link">Add new thread</a>
            </div>
            <div id="edit-thread-section"></div>
            <div id="add-new-thread-section" style="display: none;">
                {{ Form::open(['file' => true, 'id' => 'newthreadform', 'url' => getSiteUrl() . '/forums/' . $community->slug, 'data-validate' => 'validate'] ) }}
                    <h3>Add new thread</h3>

                    <div class="error-message" style="display: none;"></div>
                    <div class="file-description-section">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="threadTitle">Title:</label>

                                <div class="col-sm-9">
                                    <input type="text" id="threadTitle" class="form-control" name="title" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="threadDescription">Description:</label>

                                <div class="col-sm-9">
                                    <textarea cols="20" rows="5" id="threadDescription" name="content" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions clearfix">

                        <div class="pull-right">
                            <button type="submit" class="btn btn-success btn-with-icon btn-upload" id="save_new_thread">Save</button>
                            <button type="button" class="btn btn-default btn-with-icon btn-cancel" id="cancel-add-new-thread">Cancel</button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
            <div class="block-loading" id="addThreadSpinner">
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

        $('#add-new-thread').on('click', function(){
            $('#add-new-thread-section').show();
            $('.add-new-item-default').hide();
        });
        $('#cancel-add-new-thread').on('click', function(){
            $('#edit-thread-section').hide();
            $('#add-new-thread-section').hide();
            $('.add-new-item-default').show();
        });

        $('#newthreadform').submit(function (e) {
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

        jQuery('.editThread').on('click', function (e) {
            e.preventDefault();
            jQuery('#addThreadSpinner').show();
            jQuery('#add-new-thread-section').hide();
            $('.add-new-item-default').hide();
            jQuery.get('/forums/{{ $community->slug }}/edit/' + jQuery(this).attr('data-id'), function (data) {
                jQuery('#edit-thread-section').show().html(data);
                jQuery('#addThreadSpinner').hide();
            });
            jQuery('html, body').animate({
                scrollTop: jQuery("#edit-thread-section").offset().top
            }, 1000);
        });

        jQuery('.btn-delete').on('click', function (e) {
            e.preventDefault();
            $('#edit-thread-section').hide();
            $('#add-new-thread-section').hide();
            var elem = jQuery(this);
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: '/forums/{{ $community->slug }}/' + elem.attr('data-id'),
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