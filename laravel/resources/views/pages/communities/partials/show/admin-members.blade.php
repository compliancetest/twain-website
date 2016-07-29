<form method="post" action="" id="groupMembersForm" name="group-members-form">

    @if($isAdmin)
        <div class="pending-requests">
            @if(!count($membershipRequests))
                <p>There are no pending membership requests.</p>
            @else
                <ul class="member-list" id="request-list">
                    @foreach($membershipRequests as $membershipRequest)
                        <?php $user = \App\User::find($membershipRequest->user_id);?>
                        <li>
                            <div class="pull-left">
                                <img width="50" height="50" alt="" class="avatar" src="{{ $user->getAvatar() }}">
                                <span class="member-info">
                                    <span class="member-name">{{ cp_get_user_fullname($membershipRequest->user_id) }}</span>
                                    <span class="member-email">{{ $user->user_email }}</span>
                                    <span class="member-activity">{{ $community->updated_at->diffForHumans() }}</span>
                                </span>
                            </div>
                            <div class="pull-right action">
                                <a class="btn btn-success btn-with-icon btn-confirm acceptRequest" href="#" data-tooltip="tooltip" title="Accept" data-id="{{ $user->ID }}" data-community="{{ $community->slug }}">Accept</a>
                                <a class="btn btn-default btn-with-icon btn-cancel rejectRequest" href="#" data-tooltip="tooltip" title="Reject" data-id="{{ $user->ID }}" data-community="{{ $community->slug }}">Reject</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="members-group-action" id="membersGroupAction">
        <ul>
            @if($isAdmin)
                <li><a data-community="{{ $community->slug }}" data-role="admin" href="#" class="changeRole">Promote to Admin</a></li>
            @endif

            <li><a data-community="{{ $community->slug }}" data-role="mod" href="#" class="changeRole">Promote to Support</a></li>
            @if($isAdmin)
                <li><a data-community="{{ $community->slug }}" data-role="member" href="#" class="changeRole">Demote to Member</a></li>
                <li><a data-community="{{ $community->slug }}" data-role="remove" href="#" class="changeRole">Remove</a></li>
            @endif
        </ul>
    </div>

    <div class="message error-message" data-role="admin" style="display: none;">Please select at least one member</div>
    <div class="message error-message" data-role="mod" style="display: none;">Please select at least one community support user</div>
    <div class="message error-message" data-role="member" style="display: none;">Please select at least one administrator</div>
    <div class="message error-message" data-role="remove" style="display: none;">Please select at least one person to remove</div>

    @if($isAdmin)
        <div class="member-type-header">Administrator</div>
        <ul class="row member-list" id="admins-list">

            @foreach($community->getAdmins() as $admin)
                <?php $user = \App\User::find($admin->user_id);?>
                <li class="col-sm-6">
                    <label>
                        <input type="checkbox" value="{{ $admin->user_id }}" name="id[]" @if($admin->user_id == Auth::user()->ID) disabled="disabled" @endif>
                        <img width="28" height="28" alt="" class="avatar" src="{{ $user->getAvatar() }}">
                    </label>
                    <span class="member-info">
                        <span class="member-name">{{ cp_get_user_fullname($user->ID) }}</span>
                        <span class="member-email">{{ $user->user_email }}</span>
                        <button type="button" class="btn btn-success btn-sm demoteToMember" data-user-id="{{ $admin->user_id }}">Demote to Member</button>
                    </span>
                </li>
            @endforeach

        </ul>
    @endif

    <div class="member-type-header">Community Support Users</div>
    <ul class="row member-list" id="mods-list">

        @foreach($community->getModerators() as $mod)
            <?php $user = \App\User::find($mod->user_id);?>
            <li class="col-sm-6">
                <label>
                    @if($isAdmin)
                        <input type="checkbox" value="{{ $mod->user_id }}" name="id[]" @if($mod->user_id == Auth::user()->ID) disabled="disabled" @endif>
                    @endif
                    <img width="28" height="28" alt="" class="avatar" src="{{ $user->getAvatar() }}">
                </label>
                <span class="member-info">
                    <span class="member-name">{{ cp_get_user_fullname($user->ID) }}</span>
                    <span class="member-email">{{ $user->user_email }}</span>
                    @if($isAdmin)
                        <button type="button" class="btn btn-success btn-sm demoteToMember" data-user-id="{{ $mod->user_id }}">Demote to Member</button>
                    @endif
                </span>
            </li>
        @endforeach

    </ul>

    <div class="member-type-header">Members</div>
    <ul class="row member-list" id="mods-list">
        @foreach($community->getMembers() as $member)
            <?php $user = \App\User::find($member->user_id);?>
            <li class="col-sm-6">
                <label>
                    <input type="checkbox" value="{{ $user->ID }}" name="id[]">
                    <img width="28" height="28" alt="" class="avatar" src="{{ $user->getAvatar() }}">
                </label>
                <span class="member-info">
                    <span class="member-name">{{ cp_get_user_fullname($user->ID) }}</span>
                    <span class="member-email">{{ $user->user_email }}</span>
                </span>
            </li>
        @endforeach
    </ul>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING</div><div class="loading-wait">Please wait...</div></div></div>
</form>

<script>
    jQuery(document).ready(function(){

        jQuery.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        });

        var membersLoadingContainer = jQuery('#groupMembersForm .block-loading');

         jQuery('.acceptRequest').on('click', function(e){
            e.preventDefault();
            membersLoadingContainer.show();
            var elem = jQuery(this);
            jQuery.ajax({
                url: '/membership/' + elem.attr('data-community') + '/accept',
                type: 'post',
                data: {
                    'user_id': elem.attr('data-id')
                },
                success: function(data){
                    jQuery('.members-management').html(data.html)
                },
                complete: function () {
                    membersLoadingContainer.hide();
                }
            });
        });

        jQuery('.rejectRequest').on('click', function(e){
            e.preventDefault();
            membersLoadingContainer.show();
            var elem = jQuery(this);
            jQuery.ajax({
                url: '/membership/' + elem.attr('data-community') + '/reject',
                type: 'post',
                data: {
                    'user_id': elem.attr('data-id')
                },
                success: function(data){
                    jQuery('.members-management').html(data.html)
                },
                complete: function () {
                    membersLoadingContainer.hide();
                }
            });
        });

        jQuery('.changeRole').click(function(e){
            jQuery('.error-message[data-role]').hide();
            e.preventDefault();

            var elem = jQuery(this);
            var userRole = elem.attr('data-role');

            if(jQuery('#groupMembersForm input:checked').length){

                membersLoadingContainer.show();
                var checkUsers = [];
                jQuery("#groupMembersForm input:checkbox:checked").each(function(){
                    checkUsers.push(jQuery(this).val());
                });
                jQuery.ajax({
                    url: '/membership/' + elem.attr('data-community') + '/changerole',
                    type: 'post',
                    data: {
                        'users': checkUsers,
                        'role': userRole
                    },
                    success: function(data){
                        jQuery('.members-management').html(data.html)
                    },
                    complete: function () {
                        membersLoadingContainer.hide();
                    }
                });
            } else {
                jQuery('.error-message[data-role="' + userRole + '"]').show();
            }
        });

        jQuery('.demoteToMember').on('click', function(e){
            e.preventDefault();
            membersLoadingContainer.show();

            var elem = jQuery(this);
            var checkUsers = [];
            checkUsers.push(elem.data('user-id'));
            jQuery.ajax({
                url: '/membership/{{ $community->slug }}/changerole',
                type: 'post',
                data: {
                    'users': checkUsers,
                    'role': 'member'
                },
                success: function(data){
                    jQuery('.members-management').html(data.html)
                },
                complete: function () {
                    membersLoadingContainer.hide();
                }
            });
        });
    });
</script>