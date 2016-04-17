<?php
$isAdmin = $community->isAdmin();

//Getting Test Suites
$args = array(
        'post_type' => 'test-suite',
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and'),
        'meta_query' => array(
                array(
                        'key' => 'community_id',
                        'value' => $community->id,
                        'compare' => '='
                )
        ),
        'orderby' => 'title',
        'order' => 'ASC'
);
$testsuites = get_posts($args);
$instances = getCommunityProfileInstatnces($community->id);

?>
<div class="community-test-data row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left col-md-6">Profile Name</th>
                    <th class="text-left">Profile Purpose</th>
                    <th>Profile Type</th>
                    <th>Created Date</th>
                    <th>Valid?</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach( $instances AS $instance )
                <tr>
                    <td>
                        <a href="#modalCopyProfileUrl-{{ $instance->id }}" data-toggle="modal">{{ $instance->profile_name }}</a>
                        <p>{{ $instance->profile_description }}</p>

                        {{-- Copy Profile URL Modal--}}
                        <div class="modal fade" id="modalCopyProfileUrl-{{ $instance->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                        Profile Instance Detail
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-sm-9">
                                                {{--@@todo-ivan: Add profile id--}}
                                                <input type="text" class="form-control" readonly id="profile-link-{{ $instance->id }}" value="http://twain.lc/get-profile?id={TODOADDPROFILEID}" />
                                            </div>
                                            <div class="col-sm-3">
                                                <button class="btn btn-success btn-with-icon btn-confirm copyProfileLink" data-clipboard-target="#profile-link-{{ $instance->id }}">Copy URL</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#" class="btn btn-success btn-with-icon btn-confirm">Download</a>
                                        <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $instance->purpose }}</td>
                    <td class="text-center">
                        {{--<a href="{!! get_site_url() !!}?td-action={{ wp_create_nonce('view-profile-type') }}&id={{ $instance->type_id }}">{{ $instance->type_name }}</a>--}}
                        <a href="{!! get_site_url() !!}/html/temp/test-data-profile-ajax.php" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalViewProfile-{{ $instance->id }}">{{ $instance->type_name }}</a>

                        {{-- View Profile Modal--}}
                        <div class="modal fade profile-modal" id="modalViewProfile-{{ $instance->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                        Profile Instance Detail
                                    </div>
                                    <div class="modal-body">
                                        <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING PROFILE</div><div class="loading-wait">Please wait...</div></div></div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#" class="btn btn-success btn-with-icon btn-confirm">Download</a>
                                        <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </td>
                    <td class="text-center">{{ formatDate($instance->created_date) }}</td>
                    <td class="text-center"><span class="item-{{ strtolower($instance->validation_status) }}"></span></td>
                    <td class="text-center text-nowrap">
                        @if($community->isAdmin())
                            <a href="/html/temp/test-data-edit-profile-ajax.php" class="btn btn-icon btn-primary btn-edit" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalEditProfile-{{ $instance->id }}" data-tooltip="tooltip" title="Edit Profile"></a>
                            <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip" title="Delete Profile"></a>

                            {{-- Edit Profile Modal--}}
                            <div class="modal fade profile-modal" id="modalEditProfile-{{ $instance->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                            Edit Profile Instance
                                        </div>
                                        <div class="modal-body">
                                            <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#" class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                                            <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                        <a href="/?td-action={{  wp_create_nonce('copy-harness-instance') }}&id={{ $instance->id }}" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip" title="Copy Profile"></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var clipboard = new Clipboard('.copyProfileLink');
    clipboard.on('success', function(e) {
        jQuery(e.trigger).parents('.form-group').before( '<div class="success-message">The profile url has been copied to clipboard.</div>');
        var messageTimeoutHandler = setTimeout(function() {
            $('.success-message').slideUp();
        }, 2000);
    });
</script>