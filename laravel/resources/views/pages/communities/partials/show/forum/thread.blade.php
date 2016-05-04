<div class="block-loading-wrapper">
    <div class="community-forums">
        <div class="success-message hide">Topic was deleted successfully!</div>
        <div class="topic-header clearfix">
            <a href="/communities/{{ $community->slug }}/forum" class="btn btn-default btn-with-icon btn-back pull-right">Back to forums</a>
            <h1>{{ $thread->title }}</h1>
            <div class="topic-description">{{ $thread->content }}</div>
        </div>
        @if(count($threadPosts))
            @foreach($threadPosts as $index => $threadPost)
                <div class="forum-post" id="post_{{ $index+1 }}">
                    <div class="forum-post-header">
                        <div class="pull-left">
                            <a href="#post_{{ $index+1 }}">#{{ $index+1 }}</a>
                            Posted at {{ formatDate($threadPost->updated_at, 'Y-m-d H:i') }}, by {{ $threadPost->user->getFullName() }}
                        </div>
                        @if($isAdmin || (Auth::check() && $thread->author_id == Auth::user()->ID))
                            <div class="pull-right post-actions">
                                <a href="#" class="editPost" data-id="{{ $threadPost->id }}">Edit</a>
                                <a href="#" class="deleteForumPost" data-id="{{ $threadPost->id }}">Delete</a>
                            </div>
                        @endif

                    </div>
                    <div class="forum-post-content">
                        {{ $threadPost->content }}
                    </div>
                </div>
            @endforeach
        @else
            <p class="empty-row ">No replies yet</p>
        @endif

        <div class="add-new-item-section">
            <div class="add-new-item-default">
                <a href="#add-new-post-section" id="add-new-post" class="add-new-download-link">Add Reply</a>
            </div>
            <div id="edit-post-section"></div>
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
            $('.add-new-item-default').hide();
        });
         $('#cancel-add-new-post').on('click', function(){
            $('#edit-post-section').hide();
            $('#add-new-post-section').hide();
            $('.add-new-item-default').show();
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
        });

        jQuery('.editPost').on('click', function (e) {
            e.preventDefault();
            jQuery('#addpostSpinner').show();
            jQuery('#add-new-post-section').hide();
            $('.add-new-item-default').hide();
            jQuery.get('/forums/{{ $community->slug }}/editpost/' + jQuery(this).attr('data-id'), function (data) {
                jQuery('#addpostSpinner').hide();
                jQuery('#edit-post-section').show().html(data);
            });
            jQuery('html, body').animate({
                scrollTop: jQuery("#edit-post-section").offset().top
            }, 1000);
        });

        jQuery('.deleteForumPost').on('click', function (e) {
            e.preventDefault();
            $('#edit-post-section').hide();
            $('#add-new-post-section').hide();
            var elem = jQuery(this);
            if (confirm('Are you sure?')) {
                $.ajax({
                    url: '/forums/{{ $community->slug }}/post/' + elem.attr('data-id'),
                    type: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (result) {
                        jQuery('.success-message').removeClass('hide');
                        setTimeout(function () {
                            jQuery('.success-message').addClass('hide');
                        }, 3000);
                        $(elem).parents('.forum-post').slideUp('slow', function () {
                            $(elem).remove();
                        });
                    }
                });
                return true;
            }
        });
    });
</script>