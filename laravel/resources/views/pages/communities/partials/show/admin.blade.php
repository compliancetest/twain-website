<div id="group_admin_page" class="tab-content white_bcg column">
    <p>{{ MESSAGE_WARNING_COMMUNITY_ADMIN }}</p>
    <div class="half left">
        <!-- Group Details Tab -->
        <div class="grid-box" id="group_details_box">

            {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Details</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">

                        <div class="field-row">
                            <label>Community Name</label>
                            <span class="input-holder">
                                <input type="text" name="title" id="group-name" aria-required="true" class="input" />
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>Community Description</label>
                            <span class="input-holder">
                                <textarea name="description" id="group-desc" aria-required="true" class="textarea"></textarea>
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>Terms and Conditions</label>
                            <span class="input-holder">
                                <textarea name="terms_and_conditions" id="terms_and_conditions"
                                          aria-required="true" class="textarea">
                                    {!! @$communityMeta['terms_and_conditions'] !!}
                                </textarea>
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>License Agreements</label>
                            <span class="input-holder">
                                <textarea name="license_agreements" id="license_agreements"
                                          aria-required="true" class="textarea">
                                    {!! @$communityMeta['terms_and_conditions'] !!}
                                </textarea>
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>Obligation to Claim</label>
                            <span class="input-holder">
                                <textarea name="obligation_for_claim" id="obligation_for_claim"
                                          aria-required="true" class="textarea">
                                    {!! @$communityMeta['license_agreements'] !!}
                                </textarea>
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>Notification Email Content</label>
                            <span class="input-holder">
                                <textarea name="notification_email_of_changes" id="notification_email_of_changes"
                                          aria-required="true" class="textarea">
                                    {!! @$communityMeta['notification_email_of_changes'] !!}
                                </textarea>
                            </span>
                            <div class="clear"></div>
                        </div>

                        <div class="field-row">
                            <label>Notify community members of changes via email</label>
                            <span class="radio-holder">
                                <label>
                                    <input type="radio" name="group-notify-members" value="1" />YES
                                </label>

                                <label>
                                    <input type="radio" name="group-notify-members" value="0" checked="checked" />NO
                                </label>
                            </span>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
            {{ Form::close() }}

        </div>
        <div class="space20"></div>
        <!-- Group Avatar -->
        <div class="grid-box" id="group_avatar_box">
            {!! Form::open(['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Display Image</h5>
                </div>

                <div class="grid-box-body">
                    <div class="column grid-row">
                        <div class="field-row">
                            <div class="grid_cell current_avatar">
                                @if(!empty($communityMeta['logo']))
                                    <img src="{{ $communityMeta['logo'] }}" title="{{ $community->title }}">
                                @else
                                    <img src="<{!! CHILD_TEMPLATE_DIRECTORY !!}/images/default-group-avatar.png" title="Default Avatar" />
                                @endif
                            </div>
                            <div class="grid_cell width300 left15">

                                <p class="field-row">
                                    Upload an image to use as an avatar for this community. The image will be shown on the main community page, and in search results.
                                </p>
                                <p class="field-row">
                                    Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.
                                </p>

                                <input type="file" name="image" id="image" class="image"  file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />

                                <div class="clear space10"></div>

                                <a href="#" class="action-btn process-btn no-submit" id="upload-image-btn">
                                    <span class="p"></span><span class="t">Upload Image</span>
                                </a>

                                @if(!empty($communityMeta['logo']))
                                    <a href="#" class="action-btn delete-btn left10"><span class="p"></span><span class="t">Delete Image</span></a>
                                @endif

                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>

            {{ Form::close() }}
        </div>

        <div class="space20"></div>


        <!-- Remove Group -->
        <div class="grid-box" id="group_remove_box">

            {!! Form::open(['id'=> 'delete-community-form', 'class' => 'standard-form', 'files' => true, 'method' => 'DELETE', 'action' => ['CommunitiesController@destroy', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Details</h5>
                </div>

                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <font color='#ce1515'>WARNING</font>: Deleting this community will completely remove ALL content associated with it. There is no way back, please be careful with this option.
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" />
                                I understand the consequences of deleting this community.
                            </label>
                        </div>
                        <div class="btn-row">
                            <a href="#" class="action-btn delete-btn" onclick="jQuery('#delete-community-form').submit();"><span class="p"></span><span class="t">DELETE COMMUNITY</span></a>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>

            {{ Form::close() }}

        </div>

    </div>

    <div class="right">
        <!-- Profile -->
        <div class="grid-box" id="group_profile_types_box">
            <div class="grid-box-header">
                <h5>Profile Types</h5>
            </div>
            <?php
            if(isset($_POST['td-action']) && wp_verify_nonce($_POST['td-action'], 'save-profile-type'))
                $isEditType = true;
            else
                $isEditType = false;
            ?>
            <div class="grid-box-body" id="profile-type-list" @if($isEditType) style="display:none" @endif>
                <?php $profileTypes = getCommunityProfileTypes($community->id); ?>
                <div class="grid-box table-box">
                    <div class="grid-box-body">

                        <div class="thead tr">
                            <div class="td td-profile-title">Name</div>
                            <div class="td td-profile-instances">Instances</div>
                            <div class="td td-profile-action">Action</div>
                            <div class="clear"></div>
                        </div>

                        <div class="tbody">

                            @if(!$profileTypes)

                                <div class="tr">
                                    <div class="td td-full">No data found</div>
                                    <div class="clear"></div>
                                </div>

                            @endif

                            @foreach($profileTypes as $row)
                                <div class="tr">
                                    <div class="td td-profile-title">
                                        {{ $row->title }}
                                        <?php
                                        $pJSON = json_decode(base64_decode($row->schema));
                                        if($pJSON->Version)
                                        {
                                            $version = array();
                                            foreach(get_object_vars($pJSON->Version) as $k=>$v)
                                            {
                                                $version[] = $v;
                                            }
                                            echo " v" . implode(".", $version);
                                        }
                                        ?>
                                    </div>
                                    <div class="td td-profile-instances">{{ $row->instances }}</div>
                                    <div class="td td-profile-action">

                                        <a href="/?td-action=<?php echo wp_create_nonce('download-profile-type')?>&type_id={!! $row->id !!}&community_id={!! $community->id !!}"
                                           class="action-btn icon-btn download-btn">
                                            <span class="p"></span>
                                            <span class="simple_tooltip radius6 no-wrap">Download Profile Type<span></span></span>
                                        </a>

                                        <a href="/?td-action=<?php echo wp_create_nonce('edit-profile-type')?>&type_id={!! $row->id !!}&community_id={!! $community->id !!}"
                                           class="action-btn blue-edit-btn icon-btn left5 profile-type-edit-btn">
                                            <span class="p"></span><span class="simple_tooltip radius6">Edit Profile Type<span></span></span>
                                        </a>

                                        <a href="/?td-action=<?php echo wp_create_nonce('delete-profile-type')?>&type_id={!! $row->id !!}&community_id={!! $community->id !!}"
                                           class="action-btn blue-delete-btn icon-btn left5 profile-type-delete-btn">
                                            <span class="p"></span><span class="simple_tooltip radius6 no-wrap">Remove Profile Type<span></span></span>
                                        </a>

                                    </div>
                                    <div class="clear"></div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
                <div class="column">
                    <a href='/?td-action=<?php echo wp_create_nonce('edit-profile-type')?>&community_id={!! $community->id !!}'
                       class="action-btn process-btn" id="add-profile-type-btn"><span class="p"></span><span class="t">Add New Profile Type</span>
                    </a>
                    <div class="clear"></div>
                </div>
            </div>

            <div id="edit-profile-type" @if($isEditType) style="display: block" @endif >

                <form name="profileTypeForm" id="profileTypeForm" action="" enctype="multipart/form-data" method="post">
                    <div class="grid-box-body column">

                        <h5><?php echo $isEditType && $_POST['type_id'] ? 'Edit' : 'Add New'?> Profile Type</h5>

                        <div class="field-row">
                            <label>Enter Schema:</label>
                            <textarea name="profile_type_text" id="profile_type_text" class="textarea">
                                <?php echo isset($_POST['profile_type_text']) ? stripslashes($_POST['profile_type_text']) : '' ?>
                            </textarea>
                        </div>

                        <div class="field-row">
                            <label>Or Select File:</label>
                            <div class="clear"></div>
                            <input type="file" name="profile_type_file" id="profile_type_file" class="input_file" value="" file-type="doc" file-extensions="(.txt or .json file)" />
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>

                        <input type="hidden" name="community_id" value="{{ $community->id }}" />
                        <input type="hidden" name="type_id" id="type_id" @if($isEditType) value="{{ $_POST['type_id'] }}" @endif />
                        <input type="hidden" name="td-action" value="<?php echo wp_create_nonce('save-profile-type')?>" />
                    </div>
                    <div class="grid-box-footer">
                        <div class="btn-row">
                            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                            <a href="#" class="action-btn cancel-btn left10"><span class="p"></span><span class="t">Cancel</span></a>
                            <div class="clear"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                jQuery('#add-profile-type-btn').click(function(){
                    jQuery('#profileTypeForm h5').html('Add New Profile Type');
                    jQuery('#profileTypeForm .message').remove();
                    jQuery('#profileTypeForm #profile_type_text').val('');
                    jQuery('#profileTypeForm #profile_type_file').val('');
                    jQuery('#edit-profile-type').fadeIn();
                    jQuery('#profile-type-list').hide();
                    return false;
                });

                jQuery('#edit-profile-type .cancel-btn').click(function(){
                    jQuery('#profile-type-list').fadeIn();
                    jQuery('#edit-profile-type').hide();
                    return false;
                });

                jQuery('#profileTypeForm').submit(function(){
                    jQuery('#profileTypeForm .message').remove();
                    if(jQuery('#profile_type_file').val() == '' && jQuery('#profile_type_text').val() == '')
                    {
                        jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">Please enter schema or select a schema file.</p>');
                        return false;
                    }
                    jQuery('#save-profile-type-box .loading b').html('SAVING PROFILE TYPE');
                    jQuery('#save-profile-type-box .loading').show();
                    return true;
                });

                jQuery('.profile-type-edit-btn').click(function(){
                    jQuery('#profileTypeForm h5').html('Edit Profile Type');
                    jQuery('#profileTypeForm #profile_type_text').val('');
                    jQuery('#profileTypeForm #profile_type_file').val('');
                    jQuery('#edit-profile-type').fadeIn();
                    jQuery('#profile-type-list').hide();
                    jQuery('#edit-profile-type .loading b').html('READING PROFILE TYPE');
                    jQuery('#edit-profile-type .loading').show();
                    jQuery('#profileTypeForm .message').remove();
                    var link = jQuery(this).attr('href');
                    jQuery.ajax({
                        url: link,
                        dataType: 'xml',
                        success: function(rsp)
                        {
                            if(jQuery(rsp).find('status').text() == 'success')
                            {
                                jQuery('#profileTypeForm #profile_type_text').val(jQuery(rsp).find('schema').text());
                                jQuery('#profileTypeForm #type_id').val(jQuery(rsp).find('id').text());
                            }else{
                                jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">' + jQuery(rsp).find('msg').text() + '</p>');
                            }
                            jQuery('#edit-profile-type .loading').hide();
                        },
                        error: function(err)
                        {
                            jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">' + err.responseText + '</p>');
                            jQuery('#edit-profile-type .loading').hide();
                        }
                    })
                    return false;
                })

                $('#group_admin_page textarea:visible').redactor({
                    air:true,
                    minHeight: 120
                })
            })
        </script>
        <div class="space20"></div>


        <!-- Memebers -->
        <div class="grid-box" id="group_members_box">
            <div class="grid-box-header">
                <h5>Members</h5>
            </div>

            <div class="grid-box-body">
                <div class="column nopaddingbottom">

                    {{ Form::open(['class' => 'group-requests-form', 'url' => 'membership/'.$community->slug.'/request']) }}

                        <?php $membershiRequests = $community->getMembershipRequests();?>

                        @if($membershiRequests)

                            <p class="nomarginbottom">The following persons wants to join the Community:</p>
                            <div class="field-row">
                                <ul id="request-list" class="member-list">
                                    @foreach($community->getMembershipRequests() as $user)
                                        <li>
                                            {!!get_avatar($user->user_id, 28) !!}
                                            <span class="member-info">
                                                <span class="m-name">{{ cp_get_user_fullname($user->user_id) }}</span><br />
                                                <span class="m-email">{{ get_userdata($user->user_id)->data->user_email }}</span>
                                                <span class="activity">{{ $community->updated_at->diffForHumans() }}</span>
                                            </span>
                                            <span class="action">
                                                <a href="#" class="action-btn process-btn no-submit"><span class="p"></span><span class="t">ACCEPT</span></a>
                                                <a href="#" class="action-btn cancel-btn"><span class="p"></span><span class="t">REJECT</span></a>
                                            </span>
                                            <div class="clear"></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        @else

                            <p>
                                There are no pending membership requests.
                            </p>

                        @endif

                    {!! Form::close() !!}
                </div>
            </div>

            <div class="grid-box-body" id="group_members_body">

                {{ Form::open(['class' => 'group-requests-form', 'url' => 'membership/'.$community->slug.'/request']) }}

                    <div class="space20"></div>
                    <div class="nav left15">
                        <ul>
                            <li><a href="#" data-action="ban">Kick &amp; Ban</a></li>
                            <li><a href="#" data-action="promote_to_mod">Promote to Support Staff</a></li>
                            <li><a href="#" data-action="promote_to_admin">Promote to Admin</a></li>
                            <li class="last-li"><a href="#" data-action="remove_from_group">Remove</a></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>

                    <!-- Administrators -->
                    <?php $admins = $community->getAdmins(); ?>

                    @if($admins)
                        <div class="field-row">
                            <p><b>Administrator</b></p>
                            <ul id="admins-list" class="member-list">
                                @foreach($admins as $admin)
                                    <li>
                                        <input type="checkbox" name="id[]" value="{{ $admin->user_id }}" class="chk" />
                                        {!!get_avatar($admin->user_id, 28) !!}
                                        <span class="member-info">
                                        <span class="m-name">{{ cp_get_user_fullname($admin->user_id) }}</span>
                                        <span class="m-email">{{ get_userdata($admin->user_id)->data->user_email }}</span>
                                        <span class="clear"></span>
                                            @if(count( $admins ) > 1)
                                                <a class="action-btn process-btn small-action-btn no-submit"
                                                   href="/membership/{{ $community->id }}/admin/demote">
                                                    Demote to Member
                                                </a>
                                            @endif
                                    </span>
                                        <div class="clear"></div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="clear"></div>
                        </div>

                    @endif

                    <!-- Moderators -->
                    <?php $moderators = $community->getModerators(); ?>

                    @if($moderators)
                        <div class="field-row">
                            <p><b>Support Staff</b></p>
                            <ul id="mods-list" class="member-list">
                                @foreach($moderators as $moderator)
                                    <li>
                                        <input type="checkbox" name="id[]" value="{{ $moderator->user_id }}" class="chk" />
                                        {!!get_avatar($moderator->user_id, 28) !!}
                                        <span class="member-info">
                                        <span class="m-name">{{ cp_get_user_fullname($moderator->user_id) }}</span>
                                        <span class="m-email">{{ get_userdata($moderator->user_id)->data->user_email }}</span>
                                        <span class="clear"></span>
                                         <a class="action-btn process-btn small-action-btn no-submit"
                                            href="/membership/{{ $community->id }}/moderators/demote">
                                             Demote to Member
                                         </a>
                                    </span>
                                        <div class="clear"></div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="clear"></div>
                        </div>

                    @endif

                    <!-- Members -->
                    <?php $users = $community->getMembers(); ?>

                    @if($users)

                        <div class="field-row">
                            <p><b>Members</b></p>

                            <ul id="members-list" class="member-list">
                                @foreach($users as $user)
                                    <li>
                                        <input type="checkbox" name="id[]" value="{{ $user->user_id }}" class="chk" />
                                        {!!get_avatar($user->user_id, 28) !!}
                                        <span class="member-info">
                                        <span class="m-name">{{ cp_get_user_fullname($user->user_id) }}</span>
                                            @if($user->is_banned) <font color="#ce1515"><i>(banned)</i></font> @endif <br />
                                        <span class="m-email">{{ get_userdata($user->user_id)->data->user_email }}</span>
                                        <span class="clear"></span>
                                            @if($user->is_banned)
                                                <a class="action-btn process-btn small-action-btn no-submit"
                                                   title="Unban this member"
                                                   href="/membership/{{ $community->id }}/role/demote">
                                                    Unban
                                                </a>
                                            @endif
                                    </span>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="clear"></div>
                        </div>

                    @else

                        <div class="field-row">
                            <p><b>Members</b></p>
                            This group has no members.
                        </div>

                    @endif

                    <div class="space15"></div>
                </form>
            </div>

        </div>
        <div class="space20"></div>
        <!-- Group Privacy -->

        <div class="grid-box" id="group_privacy_box">

            {!! Form::model($community, ['id'=> 'community-settings-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Privacy Options</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <label>
                                <input type="radio" name="status" value="public" {!! isChecked('public', @$community->status) !!} />
                                <b>This is a public community</b>
                            </label>
                            <ul>
                                <li>Any site member can join this community.</li>
                                <li>This community will be listed in the communities directory and in search results.</li>
                                <li>Community content and activity will be visible to any site member.</li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="status" value="private" {!! isChecked('private', @$community->status) !!} />
                                <b>This is a private community</b>
                            </label>
                            <ul>
                                <li>Only users who request membership and are accepted can join the community.</li>
                                <li>This community will be listed in the communities directory and in search results.</li>
                                <li>Community content and activity will only be visible to members of the community.</li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="status" value="hidden" {!! isChecked('hidden', @$community->status) !!} />
                                <b>This is a hidden community</b>
                            </label>
                            <ul>
                                <li>Only users who are invited can join the community.</li>
                                <li>This community will not be listed in the communities directory or search results.</li>
                                <li>Community content and activity will only be visible to members of the community.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {!! Form::hidden('redirect', $community->getUrl() . 'admin') !!}

                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p" onclick="jQuery('#community-settings-form').submit()"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>

            {{ Form::close() }}

        </div>


        <div class="space20"></div>
        <!-- Group Invitations -->
        <div class="grid-box" id="group_invitations_box">

            {!! Form::model($community, ['id'=> 'community-settings-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Community Invitations</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            Which members of this community are allowed to invite others?
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-invite-status" value="members" {!! isChecked('members', @$communityMeta['invite_status']) !!} />
                                <b>All community members</b>
                            </label>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-invite-status" value="mods" {!! isChecked('mods', @$communityMeta['invite_status']) !!} />
                                <b>Community admins and supports only</b>
                            </label>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-invite-status" value="admins" {!! isChecked('admins', @$communityMeta['invite_status']) !!} />
                                <b>Community admins only</b>
                            </label>
                        </div>
                    </div>
                </div>

                {!! Form::hidden('redirect', $community->getUrl() . 'admin') !!}

                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>

            {{ Form::close() }}

        </div>

        <div class="space20"></div>

        <!-- Article Settings -->

        <div class="grid-box" id="group_article_settings_box">

            {!! Form::model($community, ['id'=> 'community-articles-form', 'class' => 'standard-form', 'method' => 'PATCH', 'action' => ['CommunitiesController@update', 'id' => $community->slug]]) !!}

                <div class="grid-box-header">
                    <h5>Community Articles</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <label for="wiki-enabled">
                                <input name="wiki-enabled" id="wiki-enabled" value="{{ @$communityMeta['wiki-status'] }}" @if(@$communityMeta['wiki-status']) checked="checked" @endif type="checkbox">Enable BuddyPress Docs for this group
                            </label>
                        </div>
                        <div id="community-doc-options">
                            <div class="field-row">
                                <label for="bp-docs[can-create-admins]"><?php _e( 'Minimum role to associate Article with this community:', 'bp-docs' ) ?></label>
                            </div>
                            <div class="field-row">
                                <select name="create-wiki-roles">
                                    <option value="admin" @if(@$communityMeta['wiki-roles'] == 'admin') selected="selected" @endif>Community Admin</option>
                                    <option value="mod" @if(@$communityMeta['wiki-roles'] == 'mod') selected="selected" @endif>Community Support</option>
                                    <option value="member"  @if(@$communityMeta['wiki-roles'] == 'member') selected="selected" @endif>Community Member</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn" onclick="jQuery('#community-articles-form').submit()"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
        <div class="space20"></div>

        <!-- Generate JSON -->
        <div class="grid-box" id="group_generate_json_box">

            {!! Form::open(['id'=> 'community-json-form', 'file' => true, 'action' => ['CommunitiesController@generateJson', 'id' => $community->slug]]) !!}
                <div class="grid-box-header">
                    <h5>Generate JSON</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <input type="file" name="profile_excel_file" id="profile_excel_file" class="input-file"  file-type="image" file-extensions="(.xls, .xlsx file)" />
                        <a href="#" class="action-btn process-btn no-submit left10 top3" id="upload-profile-excel-btn"><span class="p"></span><span class="t">Generate JSON</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
                @if(Session::has('zipLink'))
                    <div class="grid-box-body" id="group-generated-json">
                        <a href="{{ Session::get('zipLink') }}">json_profiles.zip</a>
                    </div>
                @endif
            {{ Form::close() }}
        </div>

        <div class="space20"></div>

    </div>
    <div class="clear"></div>
</div>
