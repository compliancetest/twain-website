<?php
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
                <tr id="test-data-row-{{ $instance->id }}">
                    <td>
                        <a href="/communityprofiles/{{ $community->slug }}/viewprofile/{{ $instance->id }}" data-target="#modalCopyProfileUrl" data-toggle="modal" data-remote="true" data-ajax-modal>{{ $instance->profile_name }}</a>
                        <p>{{ $instance->profile_description }}</p>
                    </td>
                    <td>{{ $instance->purpose }}</td>
                    <td class="text-center">
                        <a href="/profiletypes/{{ $community->slug }}/viewprofiletype/{{ $instance->type_id }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalViewProfile">{{ $instance->type_name }}</a>
                    </td>
                    <td class="text-center">{{ formatDate($instance->created_date) }}</td>
                    <td class="text-center"><span class="item-{{ strtolower($instance->validation_status) }}"></span></td>
                    <td class="text-center text-nowrap">
                        @if($community->isAdmin())
                            <a href="/communityprofiles/{{ $community->slug }}/edit/{{ $instance->id }}/{{ $instance->type_id }}" class="btn btn-icon btn-primary btn-edit" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalEditProfile" data-tooltip="tooltip" title="Edit Profile"></a>
                            <a href="#modalRemoveProfile_{{ $instance->id }}" class="btn btn-icon btn-danger btn-delete" data-toggle="modal" data-tooltip="tooltip" title="Delete Profile"></a>

                            {{-- Remove profile Confirmation Modal--}}
                            <div class="modal fade profile-modal" id="modalRemoveProfile_{{ $instance->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                            Confirm Profile Deletion
                                        </div>
                                        <div class="modal-body">
                                            Are you sure that you want to delete {{ $instance->profile_name }}?
                                        </div>
                                        <div class="modal-footer">
                                            <a data-profile-id="{{ $instance->id }}" data-profile-name="{{ $instance->profile_name }}" data-dismiss="modal" href="/communityprofiles/{{ $community->slug }}/{{ $instance->id }}" class="btn btn-success btn-with-icon btn-confirm removingProfile">Confirm</a>
                                            <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                        <a href="/communityprofiles/{{ $community->slug }}/copy/{{ $instance->id }}" class="btn btn-icon btn-primary btn-copy" data-tooltip="tooltip" title="Copy Profile"></a>
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

    jQuery(document).ready(function($) {
        jQuery('.removingProfile').on('click', function (e) {
            e.preventDefault();
            var elem = jQuery(this);
            var profile = {
                id: elem.data('profile-id'),
                name: elem.data('profile-name')
            };

            jQuery.ajax({
                type: 'delete',
                url: elem.attr('href'),
                success: function (data) {
                    if (data.status == 'success') {
                        $('#test-data-row-' + profile.id).addClass('removing').fadeTo("slow", 0.3, function () {
                            $(this).remove();
                            $('.community-test-data > .col-md-12').prepend('<div class="success-message">' + profile.name + ' has been removed</div>');
                            setTimeout(function () {
                                $('.community-test-data > .col-md-12 > .success-message').slideUp(function () {
                                    $(this).remove();
                                });
                            }, 2000);
                        });
                    }
                },
                error: function (error, status, exception) {
                    $('.community-test-data > .col-md-12').prepend('<div class="error-message">' + error + '</div>');
                    setTimeout(function () {
                        $('.community-test-data > .col-md-12 > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 2000);
                },
                complete: function () {
                    $('.modal').modal('hide');
                }
            });

        });

        jQuery('.btn-copy').on('click', function (e) {
            e.preventDefault();
            var elem = jQuery(this);
            if (confirm('Are you sure?')) {
                jQuery.ajax({
                    type: 'post',
                    url: elem.attr('href'),
                    success: function (data) {
                        if (data.status == 'success') {
                            elem.closest('tr').remove();
                        }
                    }
                });

            }
        });

        $('#modalCopyProfileUrl, #modalViewProfile').on('hidden.bs.modal', function (e) {
            $(this).find('.modal-body').html('<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
        });

    });
</script>

{{-- View Profile Modal--}}
<div class="modal fade profile-modal" id="modalViewProfile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                Profile Instance Type Detail
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

{{-- Copy Profile URL Modal--}}
<div class="modal fade profile-modal" id="modalCopyProfileUrl" tabindex="-1" role="dialog">
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

{{-- Edit Profile Modal--}}
<div class="modal fade profile-modal edit-profile-modal" id="modalEditProfile" tabindex="-1" role="dialog">
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