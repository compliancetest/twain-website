<div class="community-tab-content">

    <div class="community-admin">
        <div class="row">
            <div class="col-sm-6">
                <div class="colored-box">
                    <div class="colored-box-header">Members</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content members-management">
                            @include('pages.communities.partials.show.admin-members', ['communityRequests' => $communityRequests, 'community' => $community])
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">

                <div class="colored-box">
                    <div class="colored-box-header">Invited Users</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="table-responsive">
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

                <div class="colored-box">
                    <div class="colored-box-header">Testing Approved</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <div class="table-responsive">
                                <table class="table table-striped invitations_table">
                                <tr>
                                    <th>Organization</th>
                                    <th>Contact email</th>
                                </tr>
                                @if(count($organisations) > 0)
                                    @foreach($organisations as $organisation)
                                        <tr>
                                            <td>
                                                {{ $organisation->organisation_name }}
                                            </td>
                                            <td>{{ $organisation->contact_email }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table class="table table-bordered invitations_table">
                                                    <tr>
                                                        <th>Test Suite</th>
                                                        <th>Is Approved?</th>
                                                    </tr>

                                                    @foreach($communityTestSuites as $communityTestSuite)
                                                        <tr>
                                                            <td><a href="/test-suite/{{ $communityTestSuite->post_name }}" target="_blank"> {{ $communityTestSuite->post_title }}</a></td>
                                                            <td class="text-center">
                                                                <input type="checkbox" value="{{ $organisation->id }}" class="approveOrganisation"
                                                                       data-community="{{ $community->slug }}" data-test-suite-id="{{ \App\TestSuite::getTestSuiteFamilyMark($communityTestSuite->ID) }}"
                                                                       @if(\App\CommunityOrganisationsApprovedTestSuites::where(['organisation_id' => $organisation->id, 'community_id' => $community->id, 'test_suite_id' => $communityTestSuite->ID])->first()) checked="checked" @endif>
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                </table>
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
                    <div id="approveOrganisationSaving" class="color-box-loading">
                        <div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING</div><div class="loading-wait">Please wait...</div></div>
                    </div>
                </div>

                <div class="colored-box">
                    <div class="colored-box-header">Surveys Results Links</div>
                    <div class="colored-box-body">
                        <div class="colored-box-content">
                            <a href="{{ getSiteUrl() }}/communitysurveys/{{ $community->slug }}/surveyresults"
                               class="btn btn-primary btn-edit" data-toggle="modal" data-remote="true" data-ajax-modal
                               data-target="#modalEditSurveys" data-tooltip="tooltip" title="Edit Surveys Results Links"
                               onclick="jQuery('#editSurveysList').show();">Configure</a>
                            <div class="modal fade" id="modalEditSurveys" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            Edit Surveys Results Links
                                        </div>
                                        <div class="modal-body block-loading-wrapper">

                                        </div>
                                        <div class="modal-footer">
                                            <a href="#" class="btn btn-success btn-with-icon btn-confirm">Save</a>
                                            <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                        </div>
                                        <div class="block-loading" id="editSurveysList"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING</div><div class="loading-wait">Please wait...</div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
<script>
    jQuery(document).ready(function () {
        Page.communityAdmin.init();
    });
</script>