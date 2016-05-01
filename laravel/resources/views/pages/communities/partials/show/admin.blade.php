<div class="community-tab-content">

    <div class="community-admin">
        <div class="row">
            <div class="col-sm-6">

                <div class="colored-box">
                    <div class="colored-box-header">Details</div>
                    <div class="colored-box-body">
                        {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'data-save-method' => 'ajax', 'files' => true, 'method' => 'PATCH', 'url' => getSiteUrl() . '/communities/'.$community->slug]) !!}
                        <div class="colored-box-content">
                            <div class="form-group">
                                <label for="communityName">Community Name</label>
                                {{ Form::text('title', null, ['required' => 'required',
                                    'class' => 'form-control',
                                    'id' => 'communityName',
                                ]) }}
                            </div>
                            <div class="form-group">
                                <label for="communityDescription">Community Description</label>
                                {{ Form::textarea('description', null, ['required' => 'required',
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
                        </div>
                        <div class="colored-box-footer">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                        </div>
                        <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                        {{ Form::close() }}
                    </div>

                </div>

                {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'data-save-method' => 'ajax', 'method' => 'PATCH', 'url' => getSiteUrl() . '/communities/'.$community->slug]) !!}
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

                <div class="colored-box">
                    <div class="colored-box-header">Display Image</div>
                    <div class="colored-box-body">
                        {!! Form::model($community, ['id'=> 'group-details-image-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'files' => true, 'method' => 'PATCH', 'url' => getSiteUrl() . '/communities/'.$community->slug]) !!}
                        <div class="colored-box-content community-image-management">
                            <div class="community-image">
                                <img src="{{ $community->getImageUrl() }}" alt="">
                            </div>
                            <div class="community-avatar-description">
                                <p>Upload an image to use as an avatar for this community. The image will be shown on the main community page, and in search results.</p>
                                <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                                <div class="upload-file-field">
                                    <input type="file" name="image" class="input-file" data-file-type="image" data-file-extensions="(.jpg, .png, .gif or .jpeg file)" required data-msg-required="Please choose file" />
                                </div>
                                <button type="submit" class="btn btn-success btn-with-icon btn-add">Upload Image</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                {!! Form::open(['id'=> 'delete-community-form', 'class' => 'standard-form', 'data-validate' => 'validate', 'method' => 'DELETE', 'url' => getSiteUrl() . '/communities/'.$community->slug]) !!}
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
                            <button type="submit" class="btn btn-danger btn-with-icon btn-delete">Delete Community</button>
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
                                            <tr id="profile-type-row-{{ $profileType->id }}">
                                                <td>
                                                    {{ $profileType->title }}
                                                    <?php
                                                    $pJSON = json_decode(base64_decode($profileType->schema));
                                                    if ($pJSON->Version) {
                                                        $version = array();
                                                        foreach (get_object_vars($pJSON->Version) as $k => $v) {
                                                            $version[] = $v;
                                                        }
                                                        echo $profileTypeVersion = " v" . implode(".", $version);
                                                    }
                                                    ?>
                                                </td>
                                                <td>{{ $profileType->instances }}</td>
                                                <td class="text-nowrap">

                                                    <a href="{{ getSiteUrl() }}/profiletypes/{{ $community->slug }}/downloadprofiletype/{{ $profileType->id }}" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                    <a href="{{ getSiteUrl() }}/profiletypes/{{ $community->slug }}/edit/{{ $profileType->id }}" class="btn btn-primary btn-icon btn-edit editProfileType" data-id="{{ $profileType->id }}" data-tooltip="tooltip" title="Edit Profile Type"></a>

                                                    @if($profileType->instances == 0)
                                                        <a href="#modalRemoveProfileType_{{ $profileType->id }}" data-toggle="modal" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>

                                                        {{-- Remove profile Confirmation Modal--}}
                                                        <div class="modal fade profile-modal" id="modalRemoveProfileType_{{ $profileType->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                                                        Confirm Profile Deletion
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="default-text">Are you sure that you want to delete {{ $profileType->title }} <?php echo $profileTypeVersion; ?>?</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ getSiteUrl() }}/profiletypes/{{ $community->slug }}/{{ $profileType->id }}" data-profile-id="{{ $profileType->id }}" data-profile-name="{{ $profileType->title }} <?php echo $profileTypeVersion; ?>" data-dismiss="modal" class="btn btn-success btn-with-icon btn-confirm deleteProfileType">Confirm</a>
                                                                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3"><div class="text-center">No Data</div></td>
                                        </tr>
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                            <div class="colored-box-content">
                                <a href="#" class="btn btn-success btn-with-icon btn-add" id="addAddNewProfileType">Add New Profile Type</a>
                            </div>
                        </div>

                        <div id="addNewProfile" style="display: none;">
                            <form method="post" enctype="multipart/form-data" action="/profiletypes/{{ $community->slug }}" id="profileTypeForm" name="profileTypeForm">
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
                                    <input type="hidden" name="type_id" id="type_id" value=""/>
                                </div>
                                <div class="colored-box-footer">
                                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                                    <a class="btn btn-default btn-with-icon btn-cancel" id="cancelAddingProfile" href="#">Cancel</a>
                                </div>
                            </form>
                        </div>

                        <div id="profileTypesLoading" class="color-box-loading">
                            <div class="loading-content"><span class="loader"></span><div class="loading-text">READING PROFILE TYPE</div><div class="loading-wait">Please wait...</div></div>
                        </div>
                        <div id="profileTypesSaving" class="color-box-loading">
                            <div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING PROFILE TYPE</div><div class="loading-wait">Please wait...</div></div>
                        </div>
                        <div id="profileTypesRemoving" class="color-box-loading">
                            <div class="loading-content"><span class="loader"></span><div class="loading-text">REMOVING PROFILE TYPE</div><div class="loading-wait">Please wait...</div></div>
                        </div>
                    </div>
                </div>

                <div class="colored-box">
                    <div class="colored-box-header">Members</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content members-management">
                            @include('pages.communities.partials.show.admin-members', ['communityRequests' => $communityRequests, 'community' => $community])
                        </div>
                    </div>
                </div>

                <div class="colored-box">
                    <div class="colored-box-header">Invited Users</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                                <table class="table invitations_table">
                                <tr>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th class="text-center">Invitation Date</th>
                                    <th class="text-center">Registration Date</th>
                                </tr>
                                @if(count($invitedUsers) > 0)
                                    @foreach($invitedUsers as $invitedUser)
                                        <tr>
                                            <td>
                                                {{ $invitedUser->invitation_email }}
                                                @if(!empty($invitedUser->registered_email) && $invitedUser->invitation_email != $invitedUser->registered_email)
                                                    <br> (Registered with {{ $invitedUser->registered_email }})
                                                @endif
                                            </td>
                                            <td>{{ $invitedUser->first_name . ' ' . $invitedUser->last_name }}</td>
                                            <td>{{ $invitedUser->created_at }}</td>
                                            <td>
                                                @if($invitedUser->status == 0)
                                                    {{ $invitedUser->updated_at }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No data yet</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>


                {!! Form::open(['id'=> 'invite-user-form', 'class' => 'standard-form', 'data-save-method' => 'ajax', 'method' => 'POST', 'url' => getSiteUrl() . '/membership/'.$community->slug . '/invite']) !!}
                    <div class="colored-box">
                        <div class="colored-box-header">Invite User</div>
                        <div class="colored-box-body">
                            <div class="colored-box-content">
                                <div class="form-group">
                                    <label for="user_email">User Email (required)</label>
                                    {{ Form::text('user_email', null, ['required' => 'required',
                                        'class' => 'form-control'
                                    ]) }}
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6">
                                         <label for="first_name">First Name</label>
                                        {{ Form::text('first_name', null, ['class' => 'form-control']) }}
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="last_name">Last Name</label>
                                        {{ Form::text('last_name', null, ['class' => 'form-control']) }}
                                    </div>
                                </div>

                                @if(is_super_admin())
                                    <div class="form-group">
                                    <label for="register_automatically">
                                        {{ Form::checkbox('register_automatically', 1) }}Register automatically
                                    </label>

                                </div>
                                @endif

                            </div>
                            <div class="colored-box-footer">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Invite</button>
                            </div>
                            <div class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING YOUR DATA</div><div class="loading-wait">Please wait...</div></div></div>
                        </div>
                    </div>
                {!! Form::close() !!}

                {!! Form::model($community, ['id'=> 'group-details-form', 'class' => 'standard-form', 'files' => true, 'data-save-method' => 'ajax', 'method' => 'PATCH', 'url' => getSiteUrl() . '/communities/'.$community->slug]) !!}

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

                {!! Form::open(['id'=> 'community-json-form', 'file' => true, 'data-save-method' => 'ajax', 'method' => 'post', 'url' => getSiteUrl() . '/communities/'.$community->slug .'/getjson']) !!}
                <div class="colored-box">
                    <div class="colored-box-header">Generate JSON</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="upload-file-field">
                                <input type="file" name="profile_excel_file" class="input-file" data-file-type="image" data-file-extensions="(.xls, .xlsx file)"/>
                            </div>
                            <div class="upload-file-field-additional-btn">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Generate JSON</button>
                            </div>
                            @if(Session::has('zipLink'))
                                <a href="{{ Session::get('zipLink') }}">json_profiles.zip</a>
                            @endif
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
        Page.communityAdmin.init();
    });
</script>