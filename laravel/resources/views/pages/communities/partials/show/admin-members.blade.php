<form method="post" action="" id="groupMembersForm" name="group-members-form">
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
    <div class="members-group-action" id="membersGroupAction">
        <ul>
            <li><a data-community="{{ $community->slug }}" data-role="admin" href="#" class="changeRole">Promote to Admin</a></li>
            <li><a data-community="{{ $community->slug }}" data-role="member" href="#" class="changeRole">Demote to Member</a></li>
            <li><a data-community="{{ $community->slug }}" data-role="remove" href="#" class="changeRole">Remove</a></li>
        </ul>
    </div>

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

        jQuery('.changeRole').on('click', function(e){
            e.preventDefault();
            membersLoadingContainer.show();

            var elem = jQuery(this);
            if(jQuery('#groupMembersForm input:checked').length){
                var checkUsers = [];
                jQuery("#groupMembersForm input:checkbox:checked").each(function(){
                    checkUsers.push(jQuery(this).val());
                });
                jQuery.ajax({
                    url: '/membership/' + elem.attr('data-community') + '/changerole',
                    type: 'post',
                    data: {
                        'users': checkUsers,
                        'role': elem.attr('data-role')
                    },
                    success: function(data){
                        jQuery('.members-management').html(data.html)
                    },
                    complete: function () {
                        membersLoadingContainer.hide();
                    }
                });
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