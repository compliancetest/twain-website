<div class="community-test-data row">
    <div class="col-md-12" id="communityTestDataList">
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
                        <a href="{{ getSiteUrl() }}/communityprofiles/{{ $community->slug }}/viewprofile/{{ $instance->id }}" data-target="#modalCopyProfileUrl" data-toggle="modal" data-remote="true" data-ajax-modal>
                            {{ $instance->profile_name }}
                        </a>
                        <p>{{ $instance->profile_description }}</p>
                    </td>
                    <td>{{ $instance->purpose }}</td>
                    <td class="text-center">
                        <a href="{{ getSiteUrl() }}/profiletypes/{{ $community->slug }}/viewprofiletype/{{ $instance->type_id }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalViewProfile">{{ $instance->type_name }}</a>
                    </td>
                    <td class="text-center">{{ formatDate($instance->created_date) }}</td>
                    <td class="text-center"><span class="item-{{ strtolower($instance->validation_status) }}"></span></td>
                    <td class="text-center text-nowrap">
                        @if($community->isAdmin())
                            <a href="{{ getSiteUrl() }}/communityprofiles/{{ $community->slug }}/edit/{{ $instance->id }}/{{ $instance->type_id }}" class="btn btn-icon btn-primary btn-edit" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalEditProfile" data-tooltip="tooltip" title="Edit Profile"></a>
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
                                            <a data-profile-id="{{ $instance->id }}" data-profile-name="{{ $instance->profile_name }}" data-dismiss="modal" href="{{ getSiteUrl() }}/communityprofiles/{{ $community->slug }}/{{ $instance->id }}" class="btn btn-success btn-with-icon btn-confirm removingProfile">Confirm</a>
                                            <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

     @if($isAdmin)
        <div class="col-md-2">
            <div class="page-title-actions">
                <a href="{{ getSiteUrl() }}/communityprofiles/{{ $community->slug }}/create" class="btn btn-success btn-with-icon btn-add" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#modalCreateProfile" data-tooltip="tooltip" title="Add Profile">Add New Test Data</a>
            </div>
        </div>
        @if(count($instances))
            <div class="col-md-3">
                <div class="page-title-actions">
                    <a href="{{ getSiteUrl() }}/backups/{{ $community->slug }}/create" class="btn btn-success btn-with-icon btn-add pull-left" data-toggle="modal" data-remote="true" data-target="#modalCreateBackup" data-tooltip="tooltip" title="Create Test Data Backup">Create Test Data Backup</a>
                </div>
            </div>
        @endif


    @endif
</div>
<div class="block-loading page-loader" id="communityTestDataListLoading"><div class="loading-content"><span class="loader"></span><div class="loading-text">COPYING PROFILE</div><div class="loading-wait">Please wait...</div></div></div>
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

            if (!profile.name.length){
                profile.name = 'Profile';
            }

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
                error: function (jqXHR, status) {
                    $('.community-test-data > .col-md-12').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
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

        jQuery('.confirmBackup').on('click', function (e) {
            e.preventDefault();
            var elem = jQuery(this);

            elem.closest('.modal-content').find('.block-loading').removeClass('hidden');
            elem.closest('.modal-content').find('.modal-footer').hide();
            elem.closest('.modal-content').find('.modal-header .close_modal').hide();

            jQuery.ajax({
                type: 'post',
                url: elem.attr('href'),
                success: function (data) {
                    if (data.status == 'success') {
                        $('.community-test-data > .col-md-12').append('<div class="success-message">New Test Data Backup was created successfully!</div>');
                        setTimeout(function () {
                            $('.community-test-data > .col-md-12 > .success-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 2000);
                    }
                },
                error: function (jqXHR, status) {
                    $('.community-test-data > .col-md-12').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    setTimeout(function () {
                        $('.community-test-data > .col-md-12 > .error-message').slideUp(function () {
                            $(this).remove();
                        });
                    }, 2000);
                },
                complete: function () {
                    elem.closest('.modal-content').find('.modal-footer').show();
                    elem.closest('.modal-content').find('.modal-header .close_modal').show();
                    elem.closest('.modal-content').find('.block-loading').addClass('hidden');
                    $('.modal').modal('hide');
                }
            });

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

{{-- Create Profile Modal--}}
<div class="modal fade profile-modal edit-profile-modal" id="modalCreateProfile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                Create Profile Instance
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

{{-- Create New Test Data Backup Modal--}}
<div class="modal fade profile-modal" id="modalCreateBackup" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                New Test Data Backup
            </div>
            <div class="modal-body">
                <div class="block-loading hidden"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING DATA</div><div class="loading-wait">Please wait...</div></div></div>
                Are you sure that you want create new zip backup and upload it to S3?
            </div>
            <div class="modal-footer">
                <a href="{{ getSiteUrl() }}/communities/{{ $community->slug }}/backup" class="btn btn-success btn-with-icon btn-confirm confirmBackup">Confirm</a>
                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
            </div>
        </div>
    </div>
</div>