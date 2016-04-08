@if(Auth::check())
    <div class="community-membership-action hidden-desktop">
        @if($userEntry = $community->getMember(Auth::user()->ID))
            @if($userEntry->is_confirmed)

                @if($community->isAdmin() && count($community->getAdmins()) == 1)
                @else
                    <div class="community-membership-action hidden-mobile">
                        <a class="btn btn-danger btn-lg" href="#confirmCancelMembership"
                           data-href="/communities/popups/{{ $community->slug }}/leave" data-toggle="modal">Cancel
                            Membership</a>
                    </div>
                @endif

            @else
                <div class="community-membership-action hidden-mobile">
                    <a class="btn btn-danger btn-lg" href="#confirmCancelMembership"
                       data-href="/communities/popups/{{ $community->slug }}/leave" data-toggle="modal">Request Sent</a>
                </div>

            @endif
        @else
            <a class="btn btn-danger btn-lg joinCommunity" href="#confirmJoinCommunity" data-community-id="1">Join
                Community</a>
        @endif
    </div>

    <!-- Confirm Membership Cancellation -->
    <div class="modal fade" id="confirmCancelMembership" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup"
                            data-placement="left" data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Confirm Community Membership Cancellation
                </div>
                <div class="modal-body">
                    This will cancel your membership of the SuperStream community. Are you sure?
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel"
                            data-dismiss="modal">Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>


@endif