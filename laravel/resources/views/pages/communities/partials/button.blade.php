@if(Auth::check())
    <div class="community-membership-action">
        @if($userEntry = $community->getMember(Auth::user()->ID))
            @if($userEntry->is_confirmed)

                @if(!($community->isAdmin() && count($community->getAdmins()) == 1))
                    <a class="btn btn-danger btn-lg joinCommunity" href="#confirmCancelMembership{{ $community->id }}">Cancel Membership</a>
                @endif

            @else
                <a class="btn btn-danger btn-lg joinCommunity">Request Sent</a>
            @endif
        @else
            <a class="btn btn-danger btn-lg joinCommunity" href="#confirmJoinCommunity{{ $community->id }}">Join Community</a>
        @endif
    </div>

    <!-- Community Join Modal -->
    <div class="modal modal-small fade" id="confirmJoinCommunity{{ $community->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal"  data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                    Community Registration
                </div>
                <div class="modal-body">
                    You need to join the community of interest in order to view Test Cases
                    <div class="popup-terms-box">
                        <input type="checkbox" id="agree_community_terms" value="agree" name="agree_terms"> I agree with <a href="#readTermsAndConditions{{ $community->id }}" data-toggle="modal" data-dismiss="modal">Terms &amp; Conditions</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-success btn-with-icon btn-confirm" id="registerInCommunity" data-community-id="{{ $community->slug }}">Register</a>
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="readTermsAndConditions{{ $community->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal"
                            data-placement="left" data-target="#confirmJoinCommunity" data-toggle="modal"
                            data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Terms and Conditions
                </div>
                <div class="modal-body">
                    <div class="modal-terms-content">
                        {{ $community->meta->keyBy('meta_key')->get('terms_and_conditions')->meta_value }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-target="#confirmJoinCommunity{{ $community->id }}" onclick="$('#confirmJoinCommunity<?php echo $community->id;?> #agree_community_terms').removeAttr('checked')" data-toggle="modal" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-success btn-with-icon btn-confirm"
                       onclick="$('#confirmJoinCommunity<?php echo $community->id;?> #agree_community_terms').attr('checked', 'checked')"
                       data-target="#confirmJoinCommunity{{ $community->id }}" data-toggle="modal" data-dismiss="modal">Agree</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Membership Cancellation -->
    <div class="modal fade" id="confirmCancelMembership{{ $community->id }}" tabindex="-1" role="dialog">
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