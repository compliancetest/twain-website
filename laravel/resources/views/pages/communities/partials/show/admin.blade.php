<div class="community-tab-content">

    <div class="community-admin">
        <div class="row">
            <div class="col-sm-6">

                <div class="colored-box">
                    <div class="colored-box-header">Details</div>
                    <div class="colored-box-body">
                        {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'data-save-method' => 'ajax', 'files' => true, 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}
                        <div class="colored-box-content">
                            <div class="form-group">
                                <label for="communityName">Community Name</label>
                                {{ Form::text('title', null, ['required' => 'required',
                                    'data-msg-required' => 'Community Name is required',
                                    'class' => 'form-control',
                                    'id' => 'communityName',
                                ]) }}
                            </div>
                            <div class="form-group">
                                <label for="communityDescription">Community Description</label>
                                {{ Form::textarea('description', null, ['required' => 'required',
                                    'data-msg-required' => 'Community Description is required',
                                    'class' => 'form-control',
                                    'data-air' => 'true',
                                    'id' => 'communityDescription',
                                    'rows' => '5',
                                ]) }}
                            </div>
                            <div class="form-group">
                                <label for="communityTermsAndConditions">Terms and Conditions</label>
                                {{ Form::textarea('terms_and_conditions', @$communityMeta->get('terms_and_conditions')->meta_value, [
                                   'class' => 'form-control redactor_editor',
                                   'data-air' => 'true',
                                   'id' => 'communityTermsAndConditions',
                                   'rows' => '5',
                               ]) }}
                            </div>
                            <div class="form-group">
                                <label for="licenseAgreements">License Agreements</label>
                                {{ Form::textarea('license_agreements', @$communityMeta->get('license_agreements')->meta_value, [
                                   'class' => 'form-control redactor_editor',
                                   'data-air' => 'true',
                                   'id' => 'licenseAgreements',
                                   'rows' => '5',
                               ]) }}
                            </div>
                            <div class="form-group">
                                <label for="communityObligationToClaim">Obligation to Claim</label>
                                {{ Form::textarea('obligation_for_claim', @$communityMeta->get('obligation_for_claim')->meta_value, [
                                   'class' => 'form-control redactor_editor',
                                   'data-air' => 'true',
                                   'id' => 'communityObligationToClaim',
                                   'rows' => '5',
                               ]) }}
                            </div>
                            <div class="form-group">
                                <label for="communityNotificationEmailContent">Notification Email Content</label>
                                {{ Form::textarea('notification_email_of_changes', @$communityMeta->get('notification_email_of_changes')->meta_value, [
                                    'class' => 'form-control redactor_editor',
                                    'data-air' => 'true',
                                    'id' => 'communityNotificationEmailContent',
                                    'rows' => '5',
                                ]) }}
                            </div>
                            {{--<div class="form-group">--}}
                            {{--<label>Notify community members of changes via email</label><br>--}}
                            {{--<label>--}}
                            {{--<input type="radio" name="group-notify-members" value="1"> Yes--}}
                            {{--</label>--}}
                            {{--<label>--}}
                            {{--<input type="radio" name="group-notify-members" value="0"> No--}}
                            {{--</label>--}}
                            {{--</div>--}}
                        </div>
                        <div class="colored-box-footer">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                        </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                        {{ Form::close() }}
                    </div>

                </div>

                <div class="colored-box">
                    <div class="colored-box-header">Display Image</div>
                    <div class="colored-box-body">
                        {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'files' => true, 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}
                        <div class="colored-box-content community-image-management">
                            <div class="community-image">
                                <img src="{{ $community->getImageUrl() }}" alt="">
                            </div>
                            <div class="community-avatar-description">
                                <p>Upload an image to use as an avatar for this community. The image will be shown on
                                    the main community page, and in search results.</p>

                                <p>Click below to select a JPG, GIF or PNG format photo from your computer and then
                                    click 'Upload Image' to proceed.</p>

                                <div class="upload-file-field">
                                    <input type="file" name="image" class="input-file" data-file-type="image"
                                           data-file-extensions="(.jpg, .png, .gif or .jpeg file)"/>
                                </div>
                                <a href="#" class="btn btn-success btn-with-icon btn-add"
                                   onclick="jQuery(this).closest('form').submit()">Upload Image</a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                {!! Form::open(['id'=> 'delete-community-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'method' => 'DELETE', 'action' => ['CommunitiesController@destroy', 'id' => $community->slug]]) !!}
                <div class="colored-box">
                    <div class="colored-box-header">Details</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <p><span style="color: #ce1515;">WARNING</span>: Deleting this community will completely
                                remove ALL content associated with it. There is no way back, please be careful with this
                                option.</p>

                            <div class="form-group">
                                <label>
                                    {{ Form::checkbox('delete-group-understand', 1, null, ['required' => 'required',
                                        'data-msg-required' => 'This field is required',
                                        'id' => 'delete-group-understand',
                                    ]) }}
                                    I understand the consequences of deleting
                                    this community.
                                </label>
                            </div>
                            <a href="#" class="btn btn-danger btn-with-icon btn-delete" onclick="">Delete Community</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

            </div>

            <div class="col-sm-6">
                <div class="colored-box">
                    <div class="colored-box-header">Profile Types</div>
                    <div class="colored-box-body">
                        <div id="profileTypeList">
                            <div class="table-responsive">
                                <table class="colored-table profile-type-list">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Instances</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>

                                    @if($profileTypes)
                                        @foreach($profileTypes as $profileType)
                                            <tr>
                                                <td>
                                                    {{ $profileType->title }}
                                                    <?php
                                                    $pJSON = json_decode(base64_decode($profileType->schema));
                                                    if ($pJSON->Version) {
                                                        $version = array();
                                                        foreach (get_object_vars($pJSON->Version) as $k => $v) {
                                                            $version[] = $v;
                                                        }
                                                        echo " v" . implode(".", $version);
                                                    }
                                                    ?>
                                                </td>
                                                <td>{{ $profileType->instances }}</td>
                                                <td class="text-nowrap">
                                                    <a href="/?td-action={{ wp_create_nonce('download-profile-type') }}&type_id={{ $profileType->id }}&community_id={{ $community->id }}"
                                                       class="btn btn-success btn-icon btn-download"
                                                       data-tooltip="tooltip" title="Download Profile Type"></a>
                                                    <a href="/?td-action={{ wp_create_nonce('edit-profile-type') }}&type_id={{ $profileType->id }}&community_id={{ $community->id }}"
                                                       class="btn btn-primary btn-icon btn-edit editProfileType"
                                                       data-id="{{ $profileType->id }}"
                                                       data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                    @if($profileType->instances == 0)
                                                        <a href="/?td-action={{ wp_create_nonce('delete-profile-type') }}&type_id={{ $profileType->id }}&community_id={{ $community->id }}"
                                                           class="btn btn-danger btn-icon btn-delete"
                                                           data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3">No Data</td>
                                        </tr>
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                            <div class="colored-box-content">
                                <a href="#" class="btn btn-success btn-with-icon btn-add" id="addAddNewProfileType">Add
                                    New Profile Type</a>
                            </div>
                        </div>

                        <div id="addNewProfile" style="display: none;">
                            <form method="post" enctype="multipart/form-data" action="/" id="profileTypeForm"
                                  name="profileTypeForm">
                                <div class="colored-box-content">
                                    <div class="add-profile-title">Add New Profile Type</div>
                                    <div class="form-group">
                                        <label>Enter Schema:</label>
                                        <textarea class="form-control" rows="20" id="profile_type_text"
                                                  name="profile_type_text"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Or Select File:</label><br/>

                                        <div class="upload-file-field">
                                            <input type="file" name="profile_type_file" id="profile_type_file"
                                                   class="input-file" data-file-type="doc"
                                                   data-file-extensions="(.txt or .json file)"/>
                                        </div>
                                    </div>

                                    <input type="hidden" name="community_id" value="{{ $community->id }}"/>
                                    <input type="hidden" name="type_id" id="type_id" value=""/>
                                    <input type="hidden" name="td-action"
                                           value="{!! wp_create_nonce('save-profile-type') !!}"/>
                                </div>
                                <div class="colored-box-footer">
                                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save
                                    </button>
                                    <a class="btn btn-default btn-with-icon btn-cancel" id="cancelAddingProfile"
                                       href="#">Cancel</a>
                                </div>
                            </form>
                        </div>

                        <div id="profileTypesLoading" class="color-box-loading">
                            <div class="loading-content"><span class="loader"></span>

                                <div class="loading-text">READING PROFILE TYPE</div>
                                <div class="loading-wait">Please wait...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="colored-box">
                    <div class="colored-box-header">Members</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content members-management">
                            <form method="post" action="communities-admin.php" id="groupMembersForm"
                                  name="group-members-form">
                                <div class="pending-requests">
                                    @if(!count($membershipRequests))
                                        <p>There are no pending membership requests.</p>
                                    @else
                                        <ul class="member-list" id="request-list">
                                            @foreach($membershipRequests as $membershipRequest)
                                                <li>
                                                    <div class="pull-left">
                                                        <img width="50" height="50" alt="" class="avatar"
                                                             src="/laravel/resources/assets/images/gravatar.jpg">
                                                                            <span class="member-info">
                                                                                <?php $user = \App\User::find($membershipRequest->user_id);?>
                                                                                <span class="member-name">{{ cp_get_user_fullname($membershipRequest->user_id) }}</span>
                                                                                <span class="member-email">{{ $user->user_email }}</span>
                                                                                <span class="member-activity">{{ $community->updated_at->diffForHumans() }}</span>
                                                                            </span>
                                                    </div>
                                                    <div class="pull-right action">
                                                        <a class="btn btn-success btn-with-icon btn-confirm" href="#"
                                                           data-tooltip="tooltip" title="Accept">Accept</a>
                                                        <a class="btn btn-default btn-with-icon btn-cancel" href="#"
                                                           data-tooltip="tooltip" title="Reject">Reject</a>
                                                    </div>
                                                </li>
                                                @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="members-group-action" id="membersGroupAction">
                                    <ul>
                                        <li><a data-action="ban" href="#">Kick &amp; Ban</a></li>
                                        <li><a data-action="promote_to_mod" href="#">Promote to Support Staff</a></li>
                                        <li><a data-action="promote_to_admin" href="#">Promote to Admin</a></li>
                                        <li><a data-action="remove_from_group" href="#">Remove</a></li>
                                    </ul>
                                </div>

                                <div class="member-type-header">Administrator</div>
                                <ul class="row member-list" id="admins-list">

                                    @foreach($community->getAdmins() as $admin)
                                        <?php $user = \App\User::find($admin->user_id);?>
                                        <li class="col-sm-6">
                                            <label>
                                                <input type="checkbox" value="{{ $admin->user_id }}" name="id[]">
                                                <img width="28" height="28" alt="" class="avatar" src="/laravel/resources/assets/images/gravatar.jpg">
                                            </label>
                                                                <span class="member-info">
                                                                    <span class="member-name">{{ cp_get_user_fullname($user->ID) }}</span>
                                                                    <span class="member-email">{{ $user->user_email }}</span>
                                                                    <a href="#" class="btn btn-success btn-sm">Demote to
                                                                        Member</a>
                                                                </span>
                                        </li>
                                    @endforeach

                                </ul>

                                <div class="member-type-header">Support Staff</div>
                                <ul class="row member-list" id="mods-list">
                                    @foreach($community->getModerators() as $mod)
                                        <?php $user = \App\User::find($mod->user_id);?>
                                        <li class="col-sm-6">
                                            <label>
                                                <input type="checkbox" value="1" name="id[]">
                                                <img width="28" height="28" alt="" class="avatar" src="/laravel/resources/assets/images/gravatar.jpg">
                                            </label>
                                                                <span class="member-info">
                                                                    <span class="member-name">{{ cp_get_user_fullname($user->ID) }}</span>
                                                                    <span class="member-email">{{ $user->user_email }}</span>
                                                                    <a href="#" class="btn btn-success btn-sm">Demote to
                                                                        Member</a>
                                                                </span>
                                        </li>
                                    @endforeach

                                    <li class="clearfix hidden-sm"></li>
                                </ul>

                                <div class="member-type-header">Members</div>
                                <ul class="row member-list" id="mods-list">
                                    @foreach($community->getMembers() as $member)
                                        <?php $user = \App\User::find($member->user_id);?>
                                        <li class="col-sm-6">
                                            <label>
                                                <input type="checkbox" value="1" name="id[]">
                                                <img width="28" height="28" alt="" class="avatar" src="/laravel/resources/assets/images/gravatar.jpg">
                                            </label>
                                                                <span class="member-info">
                                                                    <span class="member-name">{{ $user->ID }}</span>
                                                                    <span class="member-email">{{ $user->user_email }}</span>
                                                                </span>
                                        </li>
                                    @endforeach
                                </ul>


                                <input type="hidden" id="action" name="action" value=""/>
                            </form>
                        </div>
                    </div>
                </div>

                {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'data-save-method' => 'ajax', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}
                <div class="colored-box">
                    <div class="colored-box-header">Privacy Options</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="form-group">
                                <label>
                                    {{ Form::radio('visibility_status', 'public') }}
                                    <strong>This is a public community</strong>
                                </label>
                                <ul class="privacy-options-list">
                                    <li>Any site member can join this community.</li>
                                    <li>This community will be listed in the communities directory and in search
                                        results.
                                    </li>
                                    <li>Community content and activity will be visible to any site member.</li>
                                </ul>
                            </div>
                            <div class="form-group">
                                <label>
                                    {{ Form::radio('visibility_status', 'private') }}
                                    <b>This is a private community</b>
                                </label>
                                <ul class="privacy-options-list">
                                    <li>Only users who request membership and are accepted can join the community.</li>
                                    <li>This community will be listed in the communities directory and in search
                                        results.
                                    </li>
                                    <li>Community content and activity will only be visible to members of the
                                        community.
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="colored-box-footer">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                        </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                    </div>

                </div>
                {!! Form::close() !!}

                {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'data-save-method' => 'ajax', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="colored-box">
                    <div class="colored-box-header">Community Articles</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="form-group">
                                <label for="articles_enabled">
                                    {!! Form::checkbox('articles_enabled', 1,  !empty($community->articles_status)) !!}
                                    Enable Articles for this community</label>
                            </div>
                            <input type="hidden" name="change_article_status" value="1">
                        </div>
                        <div class="colored-box-footer">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                        </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                    </div>
                </div>

                {!! Form::close() !!}

                {!! Form::open(['id'=> 'community-json-form', 'file' => true, 'data-save-method' => 'ajax', 'action' => ['CommunitiesController@generateJson', 'id' => $community->slug]]) !!}
                <div class="colored-box">
                    <div class="colored-box-header">Generate JSON</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="upload-file-field">
                                <input type="file" name="profile_excel_file" class="input-file" data-file-type="image"
                                       data-file-extensions="(.xls, .xlsx file)"/>
                            </div>
                            @if(Session::has('zipLink'))
                                <a href="{{ Session::get('zipLink') }}">json_profiles.zip</a>
                            @endif
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Generate JSON</button>
                        </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                    </div>
                </div>
                {{ Form::close() }}

            </div>

        </div>

    </div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery('#addAddNewProfileType').on('click', function (e) {
            e.preventDefault();
            jQuery('#addNewProfile').show();
            jQuery('#profileTypeForm #profile_type_text').val('');
            jQuery('#profileTypeForm #type_id').val('');
        });

        jQuery('#profileTypeForm').submit(function () {
            jQuery('#profileTypeForm .error_message').remove();
            if (jQuery('#profile_type_file').val() == '' && jQuery('#profile_type_text').val() == '') {
                jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="error_message">Please enter schema or select a schema file.</p>');
                return false;
            }
            jQuery('#profileTypesLoading').show();
            return true;
        });

        jQuery('#cancelAddingProfile').on('click', function (e) {
            e.preventDefault();
            jQuery('#addNewProfile').hide();
        })
        jQuery('.editProfileType').on('click', function (e) {
            jQuery('#profileTypesLoading').show();
            e.preventDefault();
            jQuery('#type_id').val(jQuery(this).attr('data-id'));
            var link = jQuery(this).attr('href');
            jQuery.ajax({
                url: link,
                dataType: 'xml',
                success: function (rsp) {
                    if (jQuery(rsp).find('status').text() == 'success') {
                        jQuery('#addNewProfile').show();
                        jQuery('#profileTypeForm #profile_type_text').val(jQuery(rsp).find('schema').text());
                        jQuery('#profileTypeForm #type_id').val(jQuery(rsp).find('id').text());
                    }
                    jQuery('#profileTypesLoading').hide();
                }
            })
        })
    });
</script>
